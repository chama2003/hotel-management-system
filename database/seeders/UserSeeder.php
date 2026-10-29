<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@luxstay.test'], [
            'name' => 'Alex Morgan',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1 555-0100',
            'address' => '88 Harbor View, New York',
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'staff@luxstay.test'], [
            'name' => 'Jamie Chen',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+1 555-0101',
            'address' => '42 Front Desk Lane, Chicago',
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'guest@luxstay.test'], [
            'name' => 'Taylor Reed',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1 555-0102',
            'address' => '221B Baker Street, London',
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'guest2@luxstay.test'], [
            'name' => 'Priya Shah',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1 555-0103',
            'address' => '180 Ashford Avenue, Miami',
            'is_active' => true,
        ]);
    }
}
