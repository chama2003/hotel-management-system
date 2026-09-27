<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference', 'room_id', 'user_id', 'created_by',
        'check_in', 'check_out', 'guests', 'special_requests',
        'nightly_rate', 'nights', 'subtotal', 'tax_amount', 'total_amount',
        'status', 'cancelled_at', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'cancelled_at' => 'datetime',
            'nightly_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public static function generateReference(): string
    {
        return 'LX-'.now()->format('Y').'-'.str_pad((string) (static::max('id') + 1), 6, '0', STR_PAD_LEFT);
    }

    public function isCancellable(): bool
    {
        if (in_array($this->status, ['cancelled', 'checked_out', 'no_show'])) {
            return false;
        }

        $cutoffHours = config('booking.free_cancellation_hours', 48);

        return now()->lt(Carbon::parse($this->check_in)->subHours($cutoffHours));
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-emerald-100 text-emerald-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'checked_in' => 'bg-sky-100 text-sky-700',
            'checked_out' => 'bg-slate-100 text-slate-600',
            'cancelled' => 'bg-red-100 text-red-700',
            'no_show' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}
