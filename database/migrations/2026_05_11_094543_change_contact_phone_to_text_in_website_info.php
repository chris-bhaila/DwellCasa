<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wrap any existing plain-string phone values into a JSON array
        DB::table('website_info')
            ->whereNotNull('contact_phone')
            ->where('contact_phone', '!=', '')
            ->whereRaw("contact_phone NOT LIKE '[%'")
            ->update(['contact_phone' => DB::raw("JSON_ARRAY(contact_phone)")]);

        Schema::table('website_info', function ($table) {
            $table->text('contact_phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Unwrap first element back to a plain string
        DB::table('website_info')
            ->whereNotNull('contact_phone')
            ->whereRaw("contact_phone LIKE '[%'")
            ->update(['contact_phone' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(contact_phone, '$[0]'))")]);

        Schema::table('website_info', function ($table) {
            $table->string('contact_phone', 20)->nullable()->change();
        });
    }
};
