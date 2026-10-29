@extends('layouts.app')
@section('title', 'Manage Rooms — LuxStay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-serif">Manage Rooms</h1>
        <a href="{{ route('admin.rooms.create') }}" class="bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold text-sm px-4 py-2 rounded-full transition">
            <i class="fa-solid fa-plus mr-1"></i> Add Room
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
                <tr>
                    <th class="text-left py-3 px-4">Room</th><th class="text-left">Type</th><th class="text-left">Price</th>
                    <th class="text-left">Status</th><th class="text-left">Bookings</th><th class="text-right px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr class="border-t border-slate-100">
                        <td class="py-3 px-4">
                            <p class="font-medium">{{ $room->room_number }} — {{ $room->name }}</p>
                            @if(!$room->is_active)<span class="text-xs text-red-500">Inactive</span>@endif
                        </td>
                        <td class="capitalize">{{ $room->type }}</td>
                        <td>${{ number_format($room->price_per_night, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.rooms.updateStatus', $room) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg border-slate-300">
                                    @foreach(['available','occupied','cleaning','maintenance'] as $s)
                                        <option value="{{ $s }}" @selected($room->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>{{ $room->bookings_count }}</td>
                        <td class="px-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="text-slate-500 hover:text-ink-900 mr-3"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="inline" onsubmit="return confirm('Delete this room?');">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $rooms->links() }}</div>
</div>
@endsection
