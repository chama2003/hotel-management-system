<?php

namespace App\Services;

use App\Models\HotelSetting;
use App\Models\Room;
use Carbon\Carbon;

class BookingPricingService
{
    public function calculate(Room $room, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $subtotal = round((float) $room->price_per_night * $nights, 2);
        $taxRate = (float) (HotelSetting::getValue('tax_rate', config('booking.tax_rate', 0.10)) ?? config('booking.tax_rate', 0.10));
        $tax = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $tax, 2);

        return [
            'nights' => $nights,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'tax_rate' => $taxRate,
        ];
    }
}
