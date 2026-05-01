<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $patan = Location::create([
            'name'        => 'Patan',
            'slug'        => 'patan',
            'description' => 'Experience luxury in the heart of Patan, surrounded by ancient architecture and vibrant culture.',
            'address'     => 'Patan, Lalitpur, Nepal',
            'phone'       => '',
            'email'       => '',
            'is_active'   => true,
        ]);

        $thamel = Location::create([
            'name'        => 'Thamel',
            'slug'        => 'thamel',
            'description' => 'Stay in the heart of Kathmandu\'s most vibrant neighbourhood, steps from world-class dining and culture.',
            'address'     => 'Thamel, Kathmandu, Nepal',
            'phone'       => '',
            'email'       => '',
            'is_active'   => true,
        ]);

        // Assign all existing data to Patan
        $patanId = $patan->id;
        $tables = [
            'room_types',
            'rooms',
            'bookings',
            'inventory',
            'guests',
            'inquiries',
            'reviews',
            'gallery_images',
            'amenities',
            'website_info',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->whereNull('location_id')->update(['location_id' => $patanId]);
        }
    }
}