<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----------------------------------------------------------------
        // Drop the old flat inventory table
        // ----------------------------------------------------------------
        Schema::dropIfExists('inventories');

        // ----------------------------------------------------------------
        // 1. inventory_categories
        //    Defines supply/equipment categories per location.
        //    Managed by admin only. Staff cannot add categories.
        // ----------------------------------------------------------------
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->cascadeOnDelete();

            $table->string('name');

            // Ties the category to a specific item type so supply
            // categories don't bleed into equipment filters and vice versa.
            $table->enum('type', ['supply', 'equipment']);

            $table->timestamps();

            // A location cannot have two categories with the same name and type.
            $table->unique(['location_id', 'name', 'type']);
        });

        // ----------------------------------------------------------------
        // 2. inventory_items
        //    The item catalog. Defines what an item IS, not how many exist.
        //    One record per distinct item per location.
        // ----------------------------------------------------------------
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('inventory_categories')
                  ->restrictOnDelete(); // Prevent deleting a category that has items.

            $table->string('name');
            $table->enum('type', ['supply', 'equipment']);

            // Unit of measurement. Supplies only — equipment tracks individual instances.
            // e.g. "pieces", "bottles", "kg", "liters", "rolls"
            $table->string('unit')->nullable();

            // When stock drops to or below this number, status flips to low_stock.
            // Only relevant for supplies.
            $table->decimal('minimum_stock', 10, 2)->default(0);

            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['location_id', 'name', 'type']);
        });

        // ----------------------------------------------------------------
        // 3. inventory_stock
        //    Tracks the live stock count for supply items.
        //    One row per supply item. Updated atomically on each log entry.
        //    Do NOT use this table for equipment — see inventory_equipment.
        // ----------------------------------------------------------------
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')
                  ->constrained('inventory_items')
                  ->cascadeOnDelete();

            // Denormalized for fast location-scoped queries without joins.
            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->cascadeOnDelete();

            $table->decimal('quantity_on_hand', 10, 2)->default(0);

            // Derived status. Always update this atomically alongside quantity_on_hand.
            // Never let this drift from the actual quantity.
            $table->enum('status', ['available', 'low_stock', 'out_of_stock'])
                  ->default('out_of_stock');

            // Cumulative cost of current stock on hand.
            $table->decimal('total_cost', 10, 2)->default(0);

            $table->timestamps();

            // One stock row per item.
            $table->unique('inventory_item_id');
        });

        // ----------------------------------------------------------------
        // 4. inventory_equipment
        //    Each row is one physical equipment unit.
        //    A hotel with 20 TVs has 20 rows here, not one row with qty 20.
        //    This enables per-unit condition tracking and room assignment.
        // ----------------------------------------------------------------
        Schema::create('inventory_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')
                  ->constrained('inventory_items')
                  ->cascadeOnDelete();

            // Denormalized for fast location-scoped queries.
            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->cascadeOnDelete();

            // Nullable — not all equipment has a traceable serial number.
            $table->string('serial_number')->nullable();

            // The room this unit is currently assigned to.
            // Null means it is in storage / unassigned.
            $table->foreignId('current_room_id')
                  ->nullable()
                  ->constrained('rooms')
                  ->nullOnDelete();

            // Current condition. Keep this updated but always log condition
            // changes in inventory_logs so history is preserved.
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'under_repair'])
                  ->default('good');

            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])
                  ->default('available');

            $table->date('purchased_at')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------------------------------------------
        // 5. inventory_logs
        //    Immutable event log for all inventory movements.
        //    Covers both supplies and equipment in one table.
        //    inventory_equipment_id is null for supply logs and vice versa.
        //
        //    Actions:
        //      restocked        — supply stock added
        //      used             — supply consumed (room optional)
        //      assigned         — equipment moved to a room
        //      returned         — equipment returned from a room to storage
        //      condition_changed — equipment condition updated
        //      written_off      — equipment retired / permanently removed
        // ----------------------------------------------------------------
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();

            // Denormalized — mirrors the pattern in your custom Activity model.
            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->cascadeOnDelete();

            $table->foreignId('inventory_item_id')
                  ->constrained('inventory_items')
                  ->cascadeOnDelete();

            // Null for supply logs.
            $table->foreignId('inventory_equipment_id')
                  ->nullable()
                  ->constrained('inventory_equipment')
                  ->cascadeOnDelete();

            $table->enum('action', [
                'restocked',
                'used',
                'assigned',
                'returned',
                'condition_changed',
                'written_off',
            ]);

            // Quantity changed. Used for supply logs (restocked/used).
            // Null for equipment logs.
            $table->decimal('quantity', 10, 2)->nullable();

            // The room involved in this action, if any.
            $table->foreignId('room_id')
                  ->nullable()
                  ->constrained('rooms')
                  ->nullOnDelete();

            // Who performed this action.
            $table->foreignId('performed_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Cost recorded at the time of restock.
            $table->decimal('cost', 10, 2)->nullable();

            // For condition_changed: record what it changed from and to.
            $table->string('previous_condition')->nullable();
            $table->string('new_condition')->nullable();

            $table->text('notes')->nullable();

            // Logs are immutable. No updated_at.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventory_equipment');
        Schema::dropIfExists('inventory_stock');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');

        // Restore the original flat table if rolling back.
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->string('name');
            $table->enum('category', ['housekeeping', 'toiletries', 'food_beverage', 'maintenance', 'other']);
            $table->decimal('stock', 10, 2)->default(0.00);
            $table->enum('unit', ['pieces', 'kg', 'liters', 'boxes', 'rolls']);
            $table->enum('unit_condition', ['new', 'good', 'fair', 'poor', 'damaged']);
            $table->decimal('minimum_stock', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'low_stock', 'out_of_stock'])->default('available');
            $table->string('image')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }
};