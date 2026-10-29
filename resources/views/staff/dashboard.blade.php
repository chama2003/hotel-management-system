@extends('layouts.app')
@section('title', 'Front Desk — LuxStay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: 'arrivals', walkIn: false }">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-serif">Front Desk</h1>
        <button @click="walkIn = true" class="bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold text-sm px-4 py-2 rounded-full transition">
            <i class="fa-solid fa-plus mr-1"></i> New Walk-In
        </button>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold">{{ $arrivals->count() }}</div>
            <div class="text-xs text-slate-500 uppercase">Today's Arrivals</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold">{{ $departures->count() }}</div>
            <div class="text-xs text-slate-500 uppercase">Today's Departures</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold">{{ $inHouse->count() }}</div>
            <div class="text-xs text-slate-500 uppercase">Guests In-House</div>
        </div>
    </div>

    <div class="flex gap-4 border-b border-slate-200 text-sm font-medium mb-4">
        <button @click="tab='arrivals'" :class="tab==='arrivals' ? 'border-gold-500 text-ink-900' : 'border-transparent text-slate-400'" class="pb-2 border-b-2">Arrivals</button>
        <button @click="tab='departures'" :class="tab==='departures' ? 'border-gold-500 text-ink-900' : 'border-transparent text-slate-400'" class="pb-2 border-b-2">Departures</button>
        <button @click="tab='rooms'" :class="tab==='rooms' ? 'border-gold-500 text-ink-900' : 'border-transparent text-slate-400'" class="pb-2 border-b-2">Room Status</button>
    </div>

    <div x-show="tab==='arrivals'" class="space-y-3">
        @forelse($arrivals as $b)
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium">{{ $b->guest->name }} &middot; Room {{ $b->room->room_number }}</p>
                    <p class="text-xs text-slate-500">Ref {{ $b->booking_reference }} &middot; {{ $b->guests }} guest(s)</p>
                </div>
                <form method="POST" action="{{ route('staff.bookings.checkin', $b) }}">
                    @csrf
                    <button class="bg-ink-900 hover:bg-ink-800 text-white text-sm font-semibold px-4 py-2 rounded-lg">Check In</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-slate-400">No arrivals scheduled today.</p>
        @endforelse
    </div>

    <div x-show="tab==='departures'" class="space-y-3" x-cloak>
        @forelse($departures as $b)
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium">{{ $b->guest->name }} &middot; Room {{ $b->room->room_number }}</p>
                    <p class="text-xs text-slate-500">Ref {{ $b->booking_reference }} &middot; Total ${{ number_format($b->total_amount, 2) }}</p>
                </div>
                <form method="POST" action="{{ route('staff.bookings.checkout', $b) }}">
                    @csrf
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">Check Out</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-slate-400">No departures scheduled today.</p>
        @endforelse
    </div>

    <div x-show="tab==='rooms'" class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-cloak>
        @foreach($rooms as $room)
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="font-semibold">Room {{ $room->room_number }}</p>
                <p class="text-xs text-slate-400 uppercase">{{ $room->type }}</p>
                <form method="POST" action="{{ route('staff.rooms.status', $room) }}" class="mt-2">
                    @csrf
                    <select name="status" onchange="this.form.submit()"
                            class="w-full text-xs rounded-lg border-slate-300
                            {{ $room->status === 'available' ? 'text-emerald-600' : ($room->status === 'maintenance' ? 'text-red-600' : 'text-amber-600') }}">
                        @foreach(['available','occupied','cleaning','maintenance'] as $s)
                            <option value="{{ $s }}" @selected($room->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endforeach
    </div>

    <!-- Walk-in modal -->
    <div x-show="walkIn" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div @click.outside="walkIn = false" class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg">
            <h2 class="font-serif text-xl mb-4">New Walk-In Reservation</h2>
            <form method="POST" action="{{ route('staff.walkin.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="guest_name" placeholder="Guest name" required class="rounded-lg border-slate-300 text-sm">
                    <input type="email" name="guest_email" placeholder="Guest email" required class="rounded-lg border-slate-300 text-sm">
                </div>
                <input type="text" name="guest_phone" placeholder="Phone (optional)" class="w-full rounded-lg border-slate-300 text-sm">
                <select name="room_id" required class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Select room</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }} — {{ $room->name }} (${{ $room->price_per_night }}/night)</option>
                    @endforeach
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="check_in" required class="rounded-lg border-slate-300 text-sm">
                    <input type="date" name="check_out" required class="rounded-lg border-slate-300 text-sm">
                </div>
                <input type="number" name="guests" min="1" value="1" placeholder="Guests" required class="w-full rounded-lg border-slate-300 text-sm">
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="walkIn = false" class="flex-1 border border-slate-300 rounded-lg py-2 text-sm">Cancel</button>
                    <button class="flex-1 bg-ink-900 hover:bg-ink-800 text-white rounded-lg py-2 text-sm font-semibold">Create Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
