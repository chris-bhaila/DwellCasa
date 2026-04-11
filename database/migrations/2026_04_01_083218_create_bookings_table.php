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
            $table->string('booking_ref')->unique();          // e.g. DWELL-2024-0001
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_inquiry_id')->nullable()->constrained()->nullOnDelete();
 
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('num_guests')->default(1);
            $table->enum('stay_type', ['short_term', 'long_term'])->default('short_term');
 
            $table->decimal('rate_per_night', 10, 2)->nullable();
            $table->decimal('rate_per_month', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
 
            $table->enum('status', [
                'pending',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled',
                'no_show'
            ])->default('confirmed');
 
            $table->enum('payment_status', [
                'unpaid',
                'deposit_paid',
                'partially_paid',
                'fully_paid',
                'refunded'
            ])->default('unpaid');
 
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
 
            $table->index('status');
            $table->index(['check_in_date', 'check_out_date']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
 