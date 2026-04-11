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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_name')->nullable()->after('room_number');
            $table->foreignId('room_type_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_name');

            // Revert room_type_id back to non-nullable
            $table->foreignId('room_type_id')->nullable(false)->change();
        });
    }
};
