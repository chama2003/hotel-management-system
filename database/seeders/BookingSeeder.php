<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $staff = User::where('role', 'staff')->firstOrFail();
        $customers = User::where('role', 'customer')->get();
        $rooms = Room::query()->with('images')->get();

        if ($rooms->isEmpty()) {
            return;
        }

        $today = Carbon::today();

        $seedBookings = [
            [
                'room_id' => $rooms[0]->id,
                'user_id' => $customers[0]->id,
                'created_by' => $staff->id,
                'check_in' => $today->copy()->addDays(2),
                'check_out' => $today->copy()->addDays(5),
                'guests' => 2,
                'status' => 'confirmed',
                'payment_method' => 'mock_card',
            ],
            [
                'room_id' => $rooms[1]->id,
                'user_id' => $customers[1]->id,
                'created_by' => $staff->id,
                'check_in' => $today->copy()->addDays(1),
                'check_out' => $today->copy()->addDays(3),
                'guests' => 2,
                'status' => 'checked_in',
                'payment_method' => 'mock_card',
            ],
            [
                'room_id' => $rooms[2]->id,
                'user_id' => $customers[0]->id,
                'created_by' => $admin->id,
                'check_in' => $today->copy()->subDays(3),
                'check_out' => $today->copy()->subDays(1),
                'guests' => 2,
                'status' => 'checked_out',
                'payment_method' => 'cash_on_arrival',
            ],
            [
                'room_id' => $rooms[3]->id,
                'user_id' => $customers[1]->id,
                'created_by' => $admin->id,
                'check_in' => $today->copy()->addDays(10),
                'check_out' => $today->copy()->addDays(12),
                'guests' => 3,
                'status' => 'pending',
                'payment_method' => 'cash_on_arrival',
            ],
            [
                'room_id' => $rooms[4]->id,
                'user_id' => $customers[0]->id,
                'created_by' => $staff->id,
                'check_in' => $today->copy()->subDays(12),
                'check_out' => $today->copy()->subDays(8),
                'guests' => 4,
                'status' => 'cancelled',
                'payment_method' => 'mock_card',
            ],
        ];

        foreach ($seedBookings as $seed) {
            $nights = Carbon::parse($seed['check_in'])->diffInDays(Carbon::parse($seed['check_out']));
            $room = Room::findOrFail($seed['room_id']);
            $subtotal = round((float) $room->price_per_night * $nights, 2);
            $tax = round($subtotal * config('booking.tax_rate', 0.10), 2);
            $total = round($subtotal + $tax, 2);

            $booking = Booking::create([
                'booking_reference' => Booking::generateReference(),
                'room_id' => $room->id,
                'user_id' => $seed['user_id'],
                'created_by' => $seed['created_by'],
                'check_in' => $seed['check_in']->toDateString(),
                'check_out' => $seed['check_out']->toDateString(),
                'guests' => $seed['guests'],
                'nightly_rate' => $room->price_per_night,
                'nights' => $nights,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'status' => $seed['status'],
                'cancelled_at' => $seed['status'] === 'cancelled' ? now()->subDays(2) : null,
                'cancellation_reason' => $seed['status'] === 'cancelled' ? 'Customer requested cancellation' : null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $seed['payment_method'],
                'transaction_reference' => $seed['payment_method'] === 'mock_card' ? 'MOCK-'.strtoupper(uniqid()) : null,
                'amount' => $total,
                'status' => $seed['payment_method'] === 'mock_card' ? 'paid' : 'pending',
                'paid_at' => $seed['payment_method'] === 'mock_card' ? now()->subDays(2) : null,
            ]);

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => Invoice::generateNumber(),
            ]);
        }
    }
}
