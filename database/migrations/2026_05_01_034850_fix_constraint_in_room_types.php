<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropUnique('room_types_slug_unique');
            $table->unique(['slug', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropUnique(['slug', 'location_id']);
            $table->string('slug')->unique()->change();
        });
    }
};