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
        Schema::table('amenity_room_type', function (Blueprint $table) {
            $table->dropForeign('amenity_room_type_room_id_foreign');
            $table->dropColumn('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('amenity_room_type', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
