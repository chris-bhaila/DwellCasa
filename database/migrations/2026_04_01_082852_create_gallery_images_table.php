<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('filename');                     // stored file path
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('category')->nullable();         // e.g. "rooms", "exterior", "amenities"
            $table->nullableMorphs('imageable');            // polymorphic: room_type or general gallery
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
 