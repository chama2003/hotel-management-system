<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;

class RoomAvailabilityService
{
    public function isAvailable(int $roomId, string $checkIn, string $checkOut, ?int $excludeBookingId = null): bool
    {
        return ! Booking::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($excludeBookingId !== null, fn ($query) => $query->where('id', '!=', $excludeBookingId))
            ->exists();
    }

    public function assertAvailable(int $roomId, string $checkIn, string $checkOut, ?int $excludeBookingId = null): void
    {
        if (! $this->isAvailable($roomId, $checkIn, $checkOut, $excludeBookingId)) {
            throw new \RuntimeException('Sorry — this room was just booked for those dates. Please choose different dates.');
        }
    }

    public function assertAvailableForRoom(Room $room, string $checkIn, string $checkOut, ?int $excludeBookingId = null): void
    {
        $this->assertAvailable((int) $room->id, $checkIn, $checkOut, $excludeBookingId);
    }
}
