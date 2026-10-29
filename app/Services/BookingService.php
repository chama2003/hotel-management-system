<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected RoomAvailabilityService $availabilityService,
        protected BookingPricingService $pricingService,
    ) {}

    public function createForGuest(User $user, Room $room, array $data): Booking
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        return DB::transaction(function () use ($user, $room, $data, $checkIn, $checkOut) {
            $this->availabilityService->assertAvailable($room->id, $checkIn->toDateString(), $checkOut->toDateString());

            $pricing = $this->pricingService->calculate($room, $checkIn, $checkOut);
            $isCardPayment = $data['payment_method'] === 'mock_card';

            $booking = Booking::create([
                'booking_reference' => Booking::generateReference(),
                'room_id' => $room->id,
                'user_id' => $user->id,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'guests' => $data['guests'],
                'special_requests' => $data['special_requests'] ?? null,
                'nightly_rate' => $room->price_per_night,
                'nights' => $pricing['nights'],
                'subtotal' => $pricing['subtotal'],
                'tax_amount' => $pricing['tax'],
                'total_amount' => $pricing['total'],
                'status' => $isCardPayment ? 'confirmed' : 'pending',
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $data['payment_method'],
                'transaction_reference' => $isCardPayment ? 'MOCK-'.strtoupper(uniqid()) : null,
                'amount' => $pricing['total'],
                'status' => $isCardPayment ? 'paid' : 'pending',
                'paid_at' => $isCardPayment ? now() : null,
            ]);

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => Invoice::generateNumber(),
            ]);

            return $booking->fresh(['room', 'payment', 'invoice']);
        });
    }
}
