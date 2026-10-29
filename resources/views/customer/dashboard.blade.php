@extends('layouts.app')
@section('title', 'My Bookings — LuxStay')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-serif">My Bookings</h1>
        <a href="{{ route('rooms.index') }}" class="bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold text-sm px-4 py-2 rounded-full transition">
            <i class="fa-solid fa-plus mr-1"></i> New Booking
        </a>
    </div>

    @foreach ([['Upcoming', $upcoming], ['Active Stay', $active], ['Past & Cancelled', $past]] as [$title, $list])
        <h2 class="font-semibold text-slate-700 mt-8 mb-3">{{ $title }} ({{ $list->count() }})</h2>
        @if($list->isEmpty())
            <p class="text-sm text-slate-400 bg-white rounded-xl p-5 border border-slate-100">Nothing here yet.</p>
        @else
            <div class="space-y-3">
                @foreach($list as $booking)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                        <img src="{{ $booking->room->coverImage()?->url() ?? '' }}" class="w-full sm:w-24 h-24 rounded-lg object-cover bg-slate-200">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-serif">{{ $booking->room->name }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $booking->statusBadgeColor() }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                            <p class="text-sm text-slate-500">{{ $booking->check_in->format('M j') }} — {{ $booking->check_out->format('M j, Y') }} &middot; {{ $booking->nights }} night(s)</p>
                            <p class="text-sm text-slate-500">Ref: <span class="font-mono">{{ $booking->booking_reference }}</span> &middot; ${{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                        <div class="flex sm:flex-col gap-2">
                            <a href="{{ route('bookings.invoice', $booking) }}" class="text-sm text-center border border-slate-300 hover:bg-slate-50 px-3 py-1.5 rounded-lg">
                                <i class="fa-solid fa-file-invoice"></i> Invoice
                            </a>
                            @if($booking->isCancellable())
                                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?');">
                                    @csrf
                                    <button class="w-full text-sm text-center border border-red-200 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endsection
