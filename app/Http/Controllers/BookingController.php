<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /** Step 1: date + guest count for a specific room. */
    public function create(Room $room): View
    {
        abort_unless($room->is_active, 404);

        return view('bookings.create', ['room' => $room]);
    }

    /**
     * AJAX endpoint used by the date picker to grey out unavailable dates
     * and re-validate the moment the guest changes check-in/check-out.
     */
    public function checkAvailability(Request $request, Room $room)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $available = Room::isRoomAvailable($room->id, $data['check_in'], $data['check_out']);
        $nights = Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));

        return response()->json([
            'available' => $available,
            'nights' => $nights,
            'pricing' => $available ? $this->priceBreakdown($room, $nights) : null,
        ]);
    }

    /** Step 2: review pricing summary + special requests, then confirm. */
    public function store(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:'.max(1, $room->capacity)],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:mock_card,cash_on_arrival'],
        ]);

        // Re-check availability at write time to close the race-condition window
        // between the guest loading the page and submitting the form.
        if (! Room::isRoomAvailable($room->id, $data['check_in'], $data['check_out'])) {
            return back()->withErrors([
                'check_in' => 'Sorry — this room was just booked for those dates. Please choose different dates.',
            ])->withInput();
        }

        $nights = Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));
        $pricing = $this->priceBreakdown($room, $nights);

        $booking = DB::transaction(function () use ($request, $room, $data, $nights, $pricing) {
            $booking = Booking::create([
                'booking_reference' => Booking::generateReference(),
                'room_id' => $room->id,
                'user_id' => $request->user()->id,
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'guests' => $data['guests'],
                'special_requests' => $data['special_requests'] ?? null,
                'nightly_rate' => $room->price_per_night,
                'nights' => $nights,
                'subtotal' => $pricing['subtotal'],
                'tax_amount' => $pricing['tax'],
                'total_amount' => $pricing['total'],
                'status' => $data['payment_method'] === 'cash_on_arrival' ? 'confirmed' : 'pending',
            ]);

            // Mock/sandbox payment gateway: no external call, no real charge.
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'method' => $data['payment_method'],
                'transaction_reference' => $data['payment_method'] === 'mock_card'
                    ? 'MOCK-'.strtoupper(uniqid())
                    : null,
                'amount' => $pricing['total'],
                'status' => $data['payment_method'] === 'mock_card' ? 'paid' : 'pending',
                'paid_at' => $data['payment_method'] === 'mock_card' ? now() : null,
            ]);

            if ($payment->status === 'paid') {
                $booking->update(['status' => 'confirmed']);
            }

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => Invoice::generateNumber(),
            ]);

            return $booking;
        });

        return redirect()
            ->route('bookings.confirmation', $booking)
            ->with('status', 'Your reservation is confirmed! Reference: '.$booking->booking_reference);
    }

    public function confirmation(Booking $booking): View
    {
        $this->authorizeGuestOwnsBooking($booking);
        $booking->load(['room.images', 'payment', 'invoice']);

        return view('bookings.confirmation', ['booking' => $booking]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeGuestOwnsBooking($booking);
        abort_unless($booking->isCancellable(), 403, 'This booking can no longer be cancelled online.');

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason', 'Cancelled by guest'),
        ]);

        if ($booking->payment && $booking->payment->status === 'paid') {
            $booking->payment->update(['status' => 'refunded']);
        }

        return back()->with('status', 'Booking cancelled. A confirmation has been sent to your email.');
    }

    /** Streams a PDF invoice for the guest to download from their dashboard. */
    public function invoice(Booking $booking)
    {
        $this->authorizeGuestOwnsBooking($booking);
        $booking->load(['room', 'guest', 'payment', 'invoice']);

        $pdf = Pdf::loadView('bookings.invoice-pdf', ['booking' => $booking]);

        return $pdf->download($booking->invoice->invoice_number.'.pdf');
    }

    private function authorizeGuestOwnsBooking(Booking $booking): void
    {
        $user = request()->user();
        abort_unless($user->isAdmin() || $user->isStaff() || $booking->user_id === $user->id, 403);
    }

    private function priceBreakdown(Room $room, int $nights): array
    {
        $subtotal = round((float) $room->price_per_night * $nights, 2);
        $tax = round($subtotal * config('booking.tax_rate', 0.10), 2);

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => round($subtotal + $tax, 2),
            'tax_rate' => config('booking.tax_rate', 0.10),
        ];
    }
}
