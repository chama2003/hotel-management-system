<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        \App\Models\HotelSetting::query()->insert([
            ['key' => 'tax_rate', 'value' => '0.10', 'description' => 'Percentage tax applied to each booking subtotal.'],
            ['key' => 'free_cancellation_hours', 'value' => '48', 'description' => 'Number of hours before check-in that a guest may cancel without charge.'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_settings');
    }
};
