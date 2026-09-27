<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Free Wi-Fi', 'icon' => 'fa-solid fa-wifi'],
            ['name' => 'Air Conditioning', 'icon' => 'fa-solid fa-snowflake'],
            ['name' => 'Smart TV', 'icon' => 'fa-solid fa-tv'],
            ['name' => 'Mini Bar', 'icon' => 'fa-solid fa-martini-glass'],
            ['name' => 'Ocean View', 'icon' => 'fa-solid fa-water'],
            ['name' => 'Room Service', 'icon' => 'fa-solid fa-bell-concierge'],
            ['name' => 'Private Balcony', 'icon' => 'fa-solid fa-door-open'],
            ['name' => 'Jacuzzi', 'icon' => 'fa-solid fa-hot-tub-person'],
            ['name' => 'Work Desk', 'icon' => 'fa-solid fa-laptop'],
            ['name' => 'Safe Box', 'icon' => 'fa-solid fa-lock'],
        ];

        foreach ($amenities as $a) {
            Amenity::updateOrCreate(['name' => $a['name']], $a);
        }
    }
}
