<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key in bookings first
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['booking_inquiry_id']);
            $table->dropColumn('booking_inquiry_id');
        });

        Schema::dropIfExists('booking_inquiries');

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('inquiry_type', ['general', 'booking', 'amenities', 'pricing', 'other']);
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');

        Schema::create('booking_inquiries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('booking_inquiry_id')->nullable()->constrained('booking_inquiries')->nullOnDelete();
        });
    }
};
