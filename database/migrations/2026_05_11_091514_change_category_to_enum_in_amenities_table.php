<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const ENUM_VALUES = [
        'utilities', 'comfort', 'bathroom', 'kitchen', 'entertainment',
        'safety', 'transport', 'wellness', 'outdoor', 'business',
        'accessibility', 'general',
    ];

    public function up(): void
    {
        // Normalise any existing free-text values that aren't in the enum to 'general'
        $valid = implode("','", self::ENUM_VALUES);
        DB::statement("UPDATE amenities SET category = 'general' WHERE category NOT IN ('{$valid}') OR category IS NULL");

        $enumList = "'" . implode("','", self::ENUM_VALUES) . "'";
        DB::statement("ALTER TABLE amenities MODIFY category ENUM({$enumList}) NOT NULL DEFAULT 'general'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE amenities MODIFY category VARCHAR(255) NULL");
    }
};
