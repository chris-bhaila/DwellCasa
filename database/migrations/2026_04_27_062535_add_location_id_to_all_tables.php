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
        $tables = [
            'room_types',
            'rooms',
            'bookings',
            'inventory',
            'guests',
            'inquiries',
            'reviews',
            'gallery_images',
            'amenities',
            'website_info',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('location_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('locations')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'room_types',
            'rooms',
            'bookings',
            'inventory',
            'guests',
            'inquiries',
            'reviews',
            'gallery_images',
            'amenities',
            'website_info',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }
    }
};
