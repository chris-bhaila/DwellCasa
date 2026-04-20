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
    Schema::create('website_info', function (Blueprint $table) {
        $table->id();
        $table->string('front_page_sub_heading_1')->nullable();
        $table->string('front_page_main_heading')->nullable();
        $table->string('front_page_sub_heading_2')->nullable();
        $table->string('front_page_end_heading')->nullable();
        $table->string('front_page_end_sub_heading')->nullable();
        $table->string('gallery_heading')->nullable();
        $table->string('gallery_sub_heading')->nullable();
        $table->string('about_heading')->nullable();
        $table->text('about_sub_description')->nullable();
        $table->text('about_main_description')->nullable();
        $table->string('contact_sub_heading')->nullable();
        $table->string('contact_address')->nullable();
        $table->string('contact_phone')->nullable();
        $table->string('contact_email')->nullable();
        $table->time('check_in')->nullable();
        $table->time('check_out')->nullable();
        $table->string('facebook_link')->nullable();
        $table->string('instagram_link')->nullable();
        $table->text('footer_description')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('website_info');
}
};
