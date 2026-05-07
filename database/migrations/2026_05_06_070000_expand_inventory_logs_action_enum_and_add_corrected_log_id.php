<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand the action enum to include 'adjusted' and 'corrected'.
        // Blueprint::enum() cannot modify an existing column, so we use a raw statement.
        DB::statement("
            ALTER TABLE inventory_logs
            MODIFY COLUMN action ENUM(
                'restocked',
                'used',
                'assigned',
                'returned',
                'condition_changed',
                'written_off',
                'adjusted',
                'corrected'
            ) NOT NULL
        ");

        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->foreignId('corrected_log_id')
                  ->nullable()
                  ->after('notes')
                  ->constrained('inventory_logs')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('corrected_log_id');
        });

        DB::statement("
            ALTER TABLE inventory_logs
            MODIFY COLUMN action ENUM(
                'restocked',
                'used',
                'assigned',
                'returned',
                'condition_changed',
                'written_off'
            ) NOT NULL
        ");
    }
};
