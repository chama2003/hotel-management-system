<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('guests')->default(1);
            $table->text('special_requests')->nullable();
            $table->decimal('nightly_rate', 10, 2);
            $table->unsignedInteger('nights');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show',
            ])->default('pending')->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'check_in', 'check_out']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->string('transaction_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
    }
};
