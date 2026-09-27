<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.rooms.index', [
            'rooms' => Room::withCount('bookings')->orderBy('room_number')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.rooms.create', ['amenities' => Amenity::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $room = Room::create($data);
        $room->amenities()->sync($request->input('amenities', []));

        foreach ($request->input('image_urls', []) as $i => $url) {
            if (blank($url)) {
                continue;
            }
            RoomImage::create([
                'room_id' => $room->id,
                'path' => $url,
                'is_cover' => $i === 0,
                'sort_order' => $i,
            ]);
        }

        return redirect()->route('admin.rooms.index')->with('status', "Room {$room->room_number} created.");
    }

    public function edit(Room $room): View
    {
        $room->load(['amenities', 'images']);

        return view('admin.rooms.edit', [
            'room' => $room,
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validated($request, $room->id);

        $room->update($data);
        $room->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.rooms.index')->with('status', "Room {$room->room_number} updated.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        abort_if($room->bookings()->whereIn('status', ['pending', 'confirmed', 'checked_in'])->exists(), 422,
            'Cannot delete a room with active bookings. Deactivate it instead.');

        $room->delete();

        return back()->with('status', 'Room deleted.');
    }

    /** Real-time toggle used from the room list without a full edit round-trip. */
    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:available,occupied,maintenance,cleaning']]);
        $room->update(['status' => $data['status']]);

        return back()->with('status', "Room {$room->room_number} marked {$data['status']}.");
    }

    private function validated(Request $request, ?int $roomId = null): array
    {
        return $request->validate([
            'room_number' => ['required', 'string', 'max:20', 'unique:rooms,room_number,'.$roomId],
            'type' => ['required', 'in:single,standard,deluxe,suite'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:200'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
