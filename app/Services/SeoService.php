<?php

namespace App\Services;

use App\Models\PropertySetting;

class SeoService
{
    /**
     * Generate SEO meta tags for a given page.
     *
     * @param string $page - Page identifier (e.g., 'home', 'rooms', 'room.detail', 'about', 'contact', 'gallery', 'location', 'services', 'amenities', 'house-rules')
     * @param array $data - Additional data for dynamic page generation (e.g., ['room_type' => RoomType, 'title' => 'Page Title'])
     * @return array - Returns SEO meta tags array
     */
    public function generate(string $page, array $data = []): array
    {
        $defaults = $this->getDefaults();
        $siteUrl = config('app.url');
        $pageUrl = $data['url'] ?? "{$siteUrl}";

        // Page-specific SEO configuration
        $pageConfig = match ($page) {
            'home' => $this->homePageSeo($defaults),
            'rooms' => $this->roomsPageSeo($defaults),
            'room.detail' => $this->roomDetailPageSeo($defaults, $data),
            'about' => $this->aboutPageSeo($defaults),
            'contact' => $this->contactPageSeo($defaults),
            'gallery' => $this->galleryPageSeo($defaults),
            'location' => $this->locationPageSeo($defaults),
            'services' => $this->servicesPageSeo($defaults),
            'amenities' => $this->amenitiesPageSeo($defaults),
            'house-rules' => $this->houseRulesPageSeo($defaults),
            default => $this->defaultPageSeo($defaults),
        };

        // Merge with page-specific data
        $title = $pageConfig['title'] ?? $defaults['seo_default_title'];
        $description = $pageConfig['description'] ?? $defaults['seo_default_description'];
        $keywords = $pageConfig['keywords'] ?? $defaults['seo_default_keywords'];
        $image = $pageConfig['image'] ?? $defaults['seo_og_image'];

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'og:title' => $title,
            'og:description' => $description,
            'og:image' => $image,
            'og:url' => $pageUrl,
            'og:type' => 'website',
            'twitter:title' => $title,
            'twitter:description' => $description,
            'twitter:image' => $image,
            'twitter:card' => 'summary_large_image',
            'canonical' => $pageUrl,
        ];
    }

    /**
     * Get default SEO values from property settings.
     */
    private function getDefaults(): array
    {
        return [
            'seo_site_name' => PropertySetting::where('key', 'seo_site_name')->value('value') ?? config('app.name', 'DwellCasa'),
            'seo_default_title' => PropertySetting::where('key', 'seo_default_title')->value('value') ?? config('app.name') . ' - Luxury Hotel Booking',
            'seo_default_description' => PropertySetting::where('key', 'seo_default_description')->value('value') ?? 'Experience luxury accommodations and exceptional hospitality.',
            'seo_default_keywords' => PropertySetting::where('key', 'seo_default_keywords')->value('value') ?? 'hotel, booking, luxury accommodation, luxury rooms',
            'seo_og_image' => PropertySetting::where('key', 'seo_og_image')->value('value') ?? asset('images/og-image.jpg'),
        ];
    }

    private function homePageSeo(array $defaults): array
    {
        return [
            'title' => $defaults['seo_site_name'] . ' - Luxury Hotel Booking',
            'description' => $defaults['seo_default_description'],
            'keywords' => $defaults['seo_default_keywords'],
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function roomsPageSeo(array $defaults): array
    {
        return [
            'title' => 'Our Rooms & Suites - ' . $defaults['seo_site_name'],
            'description' => 'Browse our luxury room types and suites. Book your perfect stay with us.',
            'keywords' => 'rooms, suites, luxury accommodation, booking',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function roomDetailPageSeo(array $defaults, array $data): array
    {
        $roomType = $data['room_type'] ?? null;

        if (!$roomType) {
            return $this->roomsPageSeo($defaults);
        }

        return [
            'title' => $roomType->name . ' - ' . $defaults['seo_site_name'],
            'description' => substr($roomType->description ?? '', 0, 155),
            'keywords' => strtolower($roomType->name) . ', luxury room, accommodation, booking',
            'image' => $data['image'] ?? $defaults['seo_og_image'],
        ];
    }

    private function aboutPageSeo(array $defaults): array
    {
        return [
            'title' => 'About Us - ' . $defaults['seo_site_name'],
            'description' => 'Learn more about our luxury hotel, our mission, and our commitment to exceptional service.',
            'keywords' => 'about, luxury hotel, hospitality, service',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function contactPageSeo(array $defaults): array
    {
        return [
            'title' => 'Contact Us - ' . $defaults['seo_site_name'],
            'description' => 'Get in touch with us for reservations, inquiries, or feedback.',
            'keywords' => 'contact, inquiries, reservations, support',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function galleryPageSeo(array $defaults): array
    {
        return [
            'title' => 'Gallery - ' . $defaults['seo_site_name'],
            'description' => 'Browse our photo gallery showcasing the beauty of our property.',
            'keywords' => 'gallery, photos, property, showcase',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function locationPageSeo(array $defaults): array
    {
        return [
            'title' => 'Location & Map - ' . $defaults['seo_site_name'],
            'description' => 'Discover our location and get directions to our luxury property.',
            'keywords' => 'location, map, directions, address',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function servicesPageSeo(array $defaults): array
    {
        return [
            'title' => 'Services - ' . $defaults['seo_site_name'],
            'description' => 'Explore our premium services and amenities designed for your comfort.',
            'keywords' => 'services, amenities, concierge, facilities',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function amenitiesPageSeo(array $defaults): array
    {
        return [
            'title' => 'Amenities - ' . $defaults['seo_site_name'],
            'description' => 'Discover the amenities offering modern comfort and luxury.',
            'keywords' => 'amenities, facilities, comfort, luxury',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function houseRulesPageSeo(array $defaults): array
    {
        return [
            'title' => 'House Rules - ' . $defaults['seo_site_name'],
            'description' => 'Learn about our house rules to ensure a pleasant stay for all guests.',
            'keywords' => 'house rules, policies, guest guidelines',
            'image' => $defaults['seo_og_image'],
        ];
    }

    private function defaultPageSeo(array $defaults): array
    {
        return [
            'title' => $defaults['seo_default_title'],
            'description' => $defaults['seo_default_description'],
            'keywords' => $defaults['seo_default_keywords'],
            'image' => $defaults['seo_og_image'],
        ];
    }
}
