<?php

namespace Database\Seeders;

use App\Models\PropertySetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with property settings.
     */
    public function run(): void
    {
        $settings = [
            // Maps Settings
            [
                'key' => 'google_maps_embed_url',
                'group' => 'maps',
                'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.1234567890!2d-74.0060!3d40.7128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQyJzQ2LjIiTiA3NMK0MDAnMjEuNiJX!5e0!3m2!1sen!2sus!4v1234567890123',
                'type' => 'text',
                'label' => 'Google Maps Embed URL for Property',
            ],
            [
                'key' => 'google_maps_api_key',
                'group' => 'maps',
                'value' => 'YOUR_GOOGLE_MAPS_API_KEY_HERE',
                'type' => 'text',
                'label' => 'Google Maps API Key (Server-side only)',
            ],
            // SEO Settings
            [
                'key' => 'seo_site_name',
                'group' => 'seo',
                'value' => 'DwellCasa',
                'type' => 'text',
                'label' => 'Site Name for SEO',
            ],
            [
                'key' => 'seo_default_title',
                'group' => 'seo',
                'value' => 'DwellCasa - Luxury Hotel Booking',
                'type' => 'text',
                'label' => 'Default Page Title',
            ],
            [
                'key' => 'seo_default_description',
                'group' => 'seo',
                'value' => 'Experience luxury accommodations and exceptional hospitality at DwellCasa. Book your perfect stay today.',
                'type' => 'text',
                'label' => 'Default Meta Description',
            ],
            [
                'key' => 'seo_default_keywords',
                'group' => 'seo',
                'value' => 'hotel, booking, luxury accommodation, luxury rooms, hospitality',
                'type' => 'text',
                'label' => 'Default Meta Keywords',
            ],
            [
                'key' => 'seo_og_image',
                'group' => 'seo',
                'value' => 'https://via.placeholder.com/1200x630?text=DwellCasa+Luxury+Hotel',
                'type' => 'text',
                'label' => 'Default Open Graph Image URL',
            ],
        ];

        foreach ($settings as $setting) {
            PropertySetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
