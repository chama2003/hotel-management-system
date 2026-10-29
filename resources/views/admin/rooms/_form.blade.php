@csrf
@isset($room) @method('PUT') @endisset

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Room number</label>
        <input type="text" name="room_number" value="{{ old('room_number', $room->room_number ?? '') }}" required class="w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="type" required class="w-full rounded-lg border-slate-300">
            @foreach(['single','standard','deluxe','suite'] as $t)
                <option value="{{ $t }}" @selected(old('type', $room->type ?? '') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Display name</label>
        <input type="text" name="name" value="{{ old('name', $room->name ?? '') }}" required class="w-full rounded-lg border-slate-300">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" required class="w-full rounded-lg border-slate-300">{{ old('description', $room->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Capacity (guests)</label>
        <input type="number" name="capacity" min="1" max="10" value="{{ old('capacity', $room->capacity ?? 2) }}" required class="w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Price / night ($)</label>
        <input type="number" step="0.01" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night ?? '') }}" required class="w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Floor</label>
        <input type="number" name="floor" value="{{ old('floor', $room->floor ?? '') }}" class="w-full rounded-lg border-slate-300">
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $room->is_active ?? true)) class="rounded border-slate-300 text-gold-600">
            Active / bookable
        </label>
    </div>
</div>

<div class="mt-5">
    <label class="block text-sm font-medium mb-2">Amenities</label>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        @php $selected = old('amenities', isset($room) ? $room->amenities->pluck('id')->toArray() : []); @endphp
        @foreach($amenities as $a)
            <label class="flex items-center gap-2 text-sm bg-slate-50 rounded-lg px-3 py-2">
                <input type="checkbox" name="amenities[]" value="{{ $a->id }}" @checked(in_array($a->id, $selected)) class="rounded border-slate-300 text-gold-600">
                <i class="{{ $a->icon }} text-gold-600"></i> {{ $a->name }}
            </label>
        @endforeach
    </div>
</div>

@unless(isset($room))
<div class="mt-5">
    <label class="block text-sm font-medium mb-1">Image URL(s)</label>
    <p class="text-xs text-slate-400 mb-2">First image becomes the cover photo. Paste direct image URLs (one per line box).</p>
    <input type="text" name="image_urls[]" placeholder="https://..." class="w-full rounded-lg border-slate-300 mb-2">
    <input type="text" name="image_urls[]" placeholder="https://..." class="w-full rounded-lg border-slate-300">
</div>
@endunless

<button class="mt-6 bg-ink-900 hover:bg-ink-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
    {{ isset($room) ? 'Save Changes' : 'Create Room' }}
</button>
