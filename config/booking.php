<?php

return [
    // Hours before check-in that a guest may still self-cancel for free.
    'free_cancellation_hours' => env('BOOKING_FREE_CANCELLATION_HOURS', 48),

    // Flat tax rate applied to every booking subtotal.
    'tax_rate' => (float) env('BOOKING_TAX_RATE', 0.10),

    // 'mock' simulates a gateway locally with no external calls;
    // swap to 'stripe' once STRIPE_KEY / STRIPE_SECRET are set.
    'payment_gateway' => env('PAYMENT_GATEWAY', 'mock'),
];
