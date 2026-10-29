@extends('layouts.app')
@section('title', 'Booking Confirmed — LuxStay')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <i class="fa-solid fa-circle-check text-5xl text-emerald-500"></i>
    <h1 class="text-3xl font-serif mt-4">Reservation Confirmed</h1>
    <p class="text-slate-500 mt-2">Reference <span class="font-mono font-semibold text-ink-900">{{ $booking->booking_reference }}</span></p>

    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 text-left">
        <div class="flex gap-4">
            <img src="{{ $booking->room->coverImage()?->url() ?? '' }}" class="w-24 h-24 rounded-lg object-cover bg-slate-200">
            <div>
                <h2 class="font-serif text-lg">{{ $booking->room->name }}</h2>
                <p class="text-sm text-slate-500">Room {{ $booking->room->room_number }}</p>
                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $booking->statusBadgeColor() }}">{{ ucfirst($booking->status) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6 text-sm">
            <div><span class="text-slate-400">Check-in</span><p class="font-medium">{{ $booking->check_in->format('D, M j Y') }}</p></div>
            <div><span class="text-slate-400">Check-out</span><p class="font-medium">{{ $booking->check_out->format('D, M j Y') }}</p></div>
            <div><span class="text-slate-400">Nights</span><p class="font-medium">{{ $booking->nights }}</p></div>
            <div><span class="text-slate-400">Guests</span><p class="font-medium">{{ $booking->guests }}</p></div>
        </div>

        <hr class="my-4 border-slate-200">
        <div class="text-sm space-y-1">
            <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span>${{ number_format($booking->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Tax</span><span>${{ number_format($booking->tax_amount, 2) }}</span></div>
            <div class="flex justify-between font-bold text-base"><span>Total</span><span>${{ number_format($booking->total_amount, 2) }}</span></div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('bookings.invoice', $booking) }}" class="flex-1 text-center bg-ink-900 hover:bg-ink-800 text-white font-semibold py-2.5 rounded-lg transition">
                <i class="fa-solid fa-file-invoice mr-1"></i> Download Invoice (PDF)
            </a>
            <a href="{{ route('customer.dashboard') }}" class="flex-1 text-center border border-slate-300 hover:bg-slate-50 font-semibold py-2.5 rounded-lg transition">
                View My Bookings
            </a>
        </div>
    </div>
</div>
@endsection
