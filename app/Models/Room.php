<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 'type', 'name', 'description', 'capacity',
        'price_per_night', 'floor', 'status', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(RoomStatusLog::class)->latest();
    }

    public function coverImage(): ?RoomImage
    {
        return $this->images->firstWhere('is_cover', true) ?? $this->images->first();
    }

    /**
     * Scope: rooms with NO overlapping active booking in the given date range.
     * Overlap rule: existing.check_in < newCheckOut AND existing.check_out > newCheckIn.
     */
    public function scopeAvailableBetween(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->where('is_active', true)
            ->whereDoesntHave('bookings', function (Builder $b) use ($checkIn, $checkOut) {
                $b->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            });
    }

    public static function isRoomAvailable(int $roomId, string $checkIn, string $checkOut, ?int $excludeBookingId = null): bool
    {
        $conflict = Booking::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();

        return ! $conflict;
    }
}
