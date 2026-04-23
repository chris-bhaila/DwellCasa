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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['housekeeping', 'toiletries', 'food_beverage', 'maintenance', 'office', 'other']);
            $table->decimal('stock', 10, 2)->default(0);
            $table->enum('unit', ['pieces', 'kg', 'liters', 'boxes', 'rolls']);
            $table->enum('unit_condition', ['new', 'good', 'fair', 'poor', 'damaged']);
            $table->decimal('minimum_stock', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'low_stock', 'out_of_stock'])->default('available');
            $table->string('image')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
