<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        Schema::create('booking_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_ref')->unique();         // e.g. INQ-2024-0001
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
 
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
 
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('num_guests')->default(1);
            $table->enum('stay_type', ['short_term', 'long_term'])->default('short_term');
            $table->text('message')->nullable();
 
            $table->enum('status', [
                'pending',
                'reviewed',
                'confirmed',
                'cancelled',
                'expired'
            ])->default('pending');
 
            $table->text('admin_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
 
            $table->index('status');
            $table->index('check_in_date');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('booking_inquiries');
    }
};