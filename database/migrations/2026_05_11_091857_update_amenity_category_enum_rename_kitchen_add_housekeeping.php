<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const ENUM_VALUES = [
        'utilities', 'comfort', 'bathroom', 'kitchen_and_dining', 'entertainment',
        'safety', 'transport', 'wellness', 'outdoor', 'business',
        'accessibility', 'housekeeping', 'general',
    ];

    public function up(): void
    {
        // Rename existing 'kitchen' rows before altering the enum
        DB::statement("UPDATE amenities SET category = 'kitchen_and_dining' WHERE category = 'kitchen'");

        $enumList = "'" . implode("','", self::ENUM_VALUES) . "'";
        DB::statement("ALTER TABLE amenities MODIFY category ENUM({$enumList}) NOT NULL DEFAULT 'general'");
    }

    public function down(): void
    {
        $old = [
            'utilities', 'comfort', 'bathroom', 'kitchen', 'entertainment',
            'safety', 'transport', 'wellness', 'outdoor', 'business',
            'accessibility', 'general',
        ];

        // Revert renamed rows
        DB::statement("UPDATE amenities SET category = 'kitchen' WHERE category = 'kitchen_and_dining'");
        // Remove housekeeping rows (fall back to general)
        DB::statement("UPDATE amenities SET category = 'general' WHERE category = 'housekeeping'");

        $enumList = "'" . implode("','", $old) . "'";
        DB::statement("ALTER TABLE amenities MODIFY category ENUM({$enumList}) NOT NULL DEFAULT 'general'");
    }
};
