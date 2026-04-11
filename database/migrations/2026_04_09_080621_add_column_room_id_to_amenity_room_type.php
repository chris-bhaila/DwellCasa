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
        DB::table('amenity_room_type')->truncate(); // wipe orphaned rows

        Schema::table('amenity_room_type', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('amenity_room_type', function (Blueprint $table) {
            $table->dropForeign(['room_id']);  // drop constraint first
            $table->dropColumn('room_id');     // then drop column
        });
    }
};
