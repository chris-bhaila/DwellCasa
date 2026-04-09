<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();                 // e.g. "about_us_text", "google_maps_embed"
            $table->string('group')->default('general');     // e.g. "general", "contact", "seo", "social"
            $table->longText('value')->nullable();
            $table->string('type')->default('text');         // text, html, image, json
            $table->string('label')->nullable();             // human-readable label for admin panel
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('property_settings');
    }
};