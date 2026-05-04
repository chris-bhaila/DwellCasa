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
            $table->string('reviews_sub_heading')->nullable()->after('gallery_sub_heading');
            $table->string('reviews_heading')->nullable()->after('reviews_sub_heading');
        });
    }

    public function down(): void
    {
        Schema::table('website_info', function (Blueprint $table) {
            $table->dropColumn(['reviews_sub_heading', 'reviews_heading']);
        });
    }
};
