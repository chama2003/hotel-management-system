<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomStatusLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskController extends Controller
{
    /** Operational overview: today's arrivals, departures, in-house guests. */
    public function dashboard(): View
    {
        $today = now()->toDateString();

        return view('staff.dashboard', [
            'arrivals' => Booking::with(['room', 'guest'])
                ->whereDate('check_in', $today)->whereIn('status', ['pending', 'confirmed'])->get(),
            'departures' => Booking::with(['room', 'guest'])
                ->whereDate('check_out', $today)->where('status', 'checked_in')->get(),
            'inHouse' => Booking::with(['room', 'guest'])->where('status', 'checked_in')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        abort_unless(in_array($booking->status, ['pending', 'confirmed']), 422, 'Booking is not eligible for check-in.');

        $booking->update(['status' => 'checked_in']);
        $this->logRoomStatus($booking->room, 'occupied');

        return back()->with('status', "Checked in booking {$booking->booking_reference}.");
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === 'checked_in', 422, 'Booking is not currently checked in.');

        $booking->update(['status' => 'checked_out']);
        $this->logRoomStatus($booking->room, 'cleaning');

        return back()->with('status', "Checked out booking {$booking->booking_reference}. Room flagged for cleaning.");
    }

    /** Staff-created walk-in / phone reservation, bypassing the online multi-step flow. */
    public function storeWalkIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email'],
            'guest_phone' => ['nullable', 'string', 'max:30'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ]);

        if (! Room::isRoomAvailable($data['room_id'], $data['check_in'], $data['check_out'])) {
            return back()->withErrors(['room_id' => 'Room is not available for those dates.'])->withInput();
        }

        $guest = \App\Models\User::firstOrCreate(
            ['email' => $data['guest_email']],
            [
                'name' => $data['guest_name'],
                'password' => bcrypt(str()->random(16)),
                'role' => 'customer',
                'phone' => $data['guest_phone'] ?? null,
            ]
        );

        $room = Room::findOrFail($data['room_id']);
        $nights = Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));
        $subtotal = round($room->price_per_night * $nights, 2);
        $tax = round($subtotal * config('booking.tax_rate', 0.10), 2);

        $booking = Booking::create([
            'booking_reference' => Booking::generateReference(),
            'room_id' => $room->id,
            'user_id' => $guest->id,
            'created_by' => $request->user()->id,
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'guests' => $data['guests'],
            'nightly_rate' => $room->price_per_night,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
            'status' => 'confirmed',
        ]);

        return redirect()->route('staff.dashboard')->with('status', "Walk-in booking {$booking->booking_reference} created for {$guest->name}.");
    }

    public function updateRoomStatus(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:available,occupied,maintenance,cleaning'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->logRoomStatus($room, $data['status'], $data['note'] ?? null);

        return back()->with('status', "Room {$room->room_number} status updated to {$data['status']}.");
    }

    private function logRoomStatus(Room $room, string $to, ?string $note = null): void
    {
        RoomStatusLog::create([
            'room_id' => $room->id,
            'changed_by' => request()->user()?->id,
            'from_status' => $room->status,
            'to_status' => $to,
            'note' => $note,
        ]);

        $room->update(['status' => $to]);
    }
}
