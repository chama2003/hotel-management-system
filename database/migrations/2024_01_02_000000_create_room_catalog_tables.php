<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->default('fa-solid fa-check');
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('type', ['single', 'standard', 'deluxe', 'suite'])->index();
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('capacity')->default(2);
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedTinyInteger('floor')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'cleaning'])
                ->default('available')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('amenity_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->unique(['room_id', 'amenity_id']);
        });

        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('room_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_status_logs');
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('amenity_room');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('amenities');
    }
};
