<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = ['booking_id', 'invoice_number', 'pdf_path'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public static function generateNumber(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -6));
    }
}
