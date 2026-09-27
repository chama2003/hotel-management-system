<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /** Public-facing room catalog with type/price/availability filters. */
    public function index(Request $request): View
    {
        $query = Room::query()->where('is_active', true)->with(['images', 'amenities']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->availableBetween($request->string('check_in'), $request->string('check_out'));
        }

        $rooms = $query->orderBy('price_per_night')->paginate(9)->withQueryString();

        return view('rooms.index', [
            'rooms' => $rooms,
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    public function show(Room $room): View
    {
        $room->load(['images', 'amenities']);

        return view('rooms.show', ['room' => $room]);
    }
}
