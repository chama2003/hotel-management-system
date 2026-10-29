@extends('layouts.app')
@section('title', 'Rooms & Suites — LuxStay')

@section('content')
<div class="relative bg-ink-900 text-white">
    <div class="absolute inset-0 opacity-30 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600')] bg-cover bg-center"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-serif">Find Your Perfect Stay</h1>
        <p class="mt-3 text-slate-300 max-w-xl mx-auto">Browse our rooms and suites, check live availability, and book in minutes.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
    <form method="GET" class="bg-white rounded-xl shadow-xl p-5 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">CHECK-IN</label>
            <input type="date" name="check_in" value="{{ request('check_in') }}" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">CHECK-OUT</label>
            <input type="date" name="check_out" value="{{ request('check_out') }}" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">ROOM TYPE</label>
            <select name="type" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">Any type</option>
                @foreach(['single','standard','deluxe','suite'] as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">MAX PRICE / NIGHT</label>
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Any" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="flex items-end">
            <button class="w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold rounded-lg py-2.5 text-sm transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Search
            </button>
        </div>
    </form>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($rooms as $room)
            @php $cover = $room->coverImage(); @endphp
            <a href="{{ route('rooms.show', $room) }}" class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden border border-slate-100">
                <div class="h-52 overflow-hidden">
                    <img src="{{ $cover?->url() ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800' }}"
                         alt="{{ $room->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wide font-semibold text-gold-600">{{ $room->type }}</span>
                        <span class="text-xs text-slate-400"><i class="fa-solid fa-user-group"></i> Up to {{ $room->capacity }}</span>
                    </div>
                    <h3 class="text-lg font-serif mt-1">{{ $room->name }}</h3>
                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $room->description }}</p>
                    <div class="flex items-center gap-2 mt-3 text-slate-400 text-sm">
                        @foreach($room->amenities->take(3) as $a)
                            <i class="{{ $a->icon }}" title="{{ $a->name }}"></i>
                        @endforeach
                        @if($room->amenities->count() > 3)
                            <span class="text-xs">+{{ $room->amenities->count() - 3 }} more</span>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div><span class="text-xl font-bold">${{ number_format($room->price_per_night, 0) }}</span><span class="text-slate-400 text-sm"> / night</span></div>
                        <span class="text-sm font-semibold text-gold-600 group-hover:underline">View room &rarr;</span>
                    </div>
                </div>
            </a>
        @empty
            <p class="col-span-3 text-center text-slate-500 py-20">No rooms match your search. Try different dates or filters.</p>
        @endforelse
    </div>

    <div class="mt-10">{{ $rooms->links() }}</div>
</div>
@endsection
