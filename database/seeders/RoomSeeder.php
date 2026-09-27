<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'room_number' => '101', 'type' => 'single', 'name' => 'Cozy Single Room',
                'description' => 'A compact, comfortable room perfect for the solo traveller, with a plush single bed and city views.',
                'capacity' => 1, 'price_per_night' => 79.00, 'floor' => 1,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Work Desk'],
                'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200',
            ],
            [
                'room_number' => '102', 'type' => 'standard', 'name' => 'Classic Standard Room',
                'description' => 'Warm, well-appointed twin room with everything you need for a relaxed stay.',
                'capacity' => 2, 'price_per_night' => 109.00, 'floor' => 1,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Safe Box'],
                'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1200',
            ],
            [
                'room_number' => '201', 'type' => 'deluxe', 'name' => 'Deluxe King Room',
                'description' => 'Spacious deluxe room featuring a king bed, sitting area and a private balcony overlooking the gardens.',
                'capacity' => 2, 'price_per_night' => 179.00, 'floor' => 2,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Private Balcony', 'Room Service'],
                'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200',
            ],
            [
                'room_number' => '202', 'type' => 'deluxe', 'name' => 'Deluxe Ocean View',
                'description' => 'Wake up to panoramic ocean views in this bright, airy deluxe room with premium furnishings.',
                'capacity' => 3, 'price_per_night' => 219.00, 'floor' => 2,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Ocean View', 'Mini Bar', 'Room Service'],
                'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200',
            ],
            [
                'room_number' => '301', 'type' => 'suite', 'name' => 'Executive Suite',
                'description' => 'A two-room suite with a separate living area, ideal for extended stays or entertaining guests.',
                'capacity' => 4, 'price_per_night' => 349.00, 'floor' => 3,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Work Desk', 'Room Service', 'Safe Box'],
                'image' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=1200',
            ],
            [
                'room_number' => '401', 'type' => 'suite', 'name' => 'Presidential Suite',
                'description' => 'Our finest accommodation: a sprawling suite with a private jacuzzi, wraparound balcony and butler service.',
                'capacity' => 4, 'price_per_night' => 599.00, 'floor' => 4,
                'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Ocean View', 'Private Balcony', 'Jacuzzi', 'Room Service', 'Safe Box'],
                'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200',
            ],
        ];

        foreach ($rooms as $r) {
            $amenityNames = $r['amenities'];
            $image = $r['image'];
            unset($r['amenities'], $r['image']);

            $room = Room::updateOrCreate(['room_number' => $r['room_number']], $r);

            $room->amenities()->sync(Amenity::whereIn('name', $amenityNames)->pluck('id'));

            if ($room->images()->count() === 0) {
                RoomImage::create([
                    'room_id' => $room->id,
                    'path' => $image,
                    'is_cover' => true,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
