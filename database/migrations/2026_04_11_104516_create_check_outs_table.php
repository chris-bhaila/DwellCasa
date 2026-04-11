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
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_out_at');
            $table->boolean('late_check_out')->default(false);
            $table->enum('room_condition', ['good', 'damaged', 'needs_cleaning'])->default('good');
            $table->text('damage_notes')->nullable();
            $table->decimal('extra_charges', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
