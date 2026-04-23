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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
    
            // Guest info — filled for public reviews, pulled from guest for verified
            $table->string('name');
            $table->string('email');
    
            // Nullable — only set for verified guest reviews
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
    
            // Review content
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->string('title')->nullable();
            $table->text('body');
    
            // Type
            $table->enum('type', ['hotel', 'room_type'])->default('hotel');
    
            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    
            // For verified guest email link
            $table->string('review_token')->nullable()->unique();
            $table->boolean('token_used')->default(false);
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
