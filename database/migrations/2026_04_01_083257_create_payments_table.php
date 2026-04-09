<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ref')->unique();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
 
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('NPR');
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'esewa',
                'khalti',
                'card',
                'other'
            ])->default('cash');
 
            $table->enum('type', ['deposit', 'rent', 'refund', 'fee'])->default('rent');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
 
            $table->string('gateway_transaction_id')->nullable(); // for online payments
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
 
            $table->index('status');
            $table->index('paid_at');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
 