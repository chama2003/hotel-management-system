@extends('layouts.app')
@section('title', $room->name.' — LuxStay')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="{{ route('rooms.index') }}" class="text-sm text-slate-500 hover:text-gold-600"><i class="fa-solid fa-arrow-left mr-1"></i> Back to rooms</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-4">
        <div class="lg:col-span-2">
            <div class="rounded-2xl overflow-hidden shadow-lg h-96 bg-slate-200" x-data="{ active: 0 }">
                @foreach($room->images as $i => $img)
                    <img x-show="active === {{ $i }}" src="{{ $img->url() }}" class="w-full h-full object-cover">
                @endforeach
                @if($room->images->isEmpty())
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1200" class="w-full h-full object-cover">
                @endif
            </div>

            <div class="mt-8">
                <span class="text-xs uppercase tracking-wide font-semibold text-gold-600">{{ $room->type }} &middot; Room {{ $room->room_number }}</span>
                <h1 class="text-3xl font-serif mt-1">{{ $room->name }}</h1>
                <p class="text-slate-600 mt-4 leading-relaxed">{{ $room->description }}</p>

                <h3 class="font-semibold mt-8 mb-3">Amenities</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($room->amenities as $a)
                        <div class="flex items-center gap-2 text-sm text-slate-600 bg-slate-50 rounded-lg px-3 py-2">
                            <i class="{{ $a->icon }} text-gold-600"></i> {{ $a->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-6 sticky top-24">
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-bold">${{ number_format($room->price_per_night, 2) }}</span>
                    <span class="text-slate-400 text-sm">/ night</span>
                </div>
                <p class="text-sm text-slate-500 mt-1"><i class="fa-solid fa-user-group"></i> Sleeps up to {{ $room->capacity }} guests</p>

                <div class="mt-5 flex items-center gap-2 text-sm">
                    <span class="w-2.5 h-2.5 rounded-full {{ $room->status === 'available' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    {{ ucfirst($room->status) }}
                </div>

                @auth
                    <a href="{{ route('bookings.create', $room) }}"
                       class="mt-6 block text-center w-full bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold py-3 rounded-lg transition">
                        Book This Room
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="mt-6 block text-center w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold py-3 rounded-lg transition">
                        Sign in to Book
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
