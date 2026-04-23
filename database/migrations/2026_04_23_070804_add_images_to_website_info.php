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
        Schema::table('website_info', function (Blueprint $table) {
            $table->string('homepage_main_image')->nullable();
            $table->string('homepage_end_image')->nullable();
            $table->string('about_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_info', function (Blueprint $table) {
            $table->dropColumn('homepage_main_image');
            $table->dropColumn('homepage_end_image');
            $table->dropColumn('about_image');
            
        });
    }
};
