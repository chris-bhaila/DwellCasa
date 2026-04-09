<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\Service;
use App\Models\GalleryImage;
use App\Models\HouseRule;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap dynamically.
     * Includes: homepage, room types, amenities, services, gallery, location, house rules, and contact pages.
     *
     * @return Response
     */
    public function index(): Response
    {
        $siteUrl = config('app.url');
        $currentDate = now();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $sitemap .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Homepage
        $sitemap .= $this->urlEntry($siteUrl, $currentDate, 'weekly', 1.0);

        // Dynamic Pages with static high priority
        $staticPages = [
            ['url' => '/rooms', 'freq' => 'weekly', 'priority' => 0.9],
            ['url' => '/gallery', 'freq' => 'monthly', 'priority' => 0.8],
            ['url' => '/about', 'freq' => 'monthly', 'priority' => 0.7],
            ['url' => '/contact', 'freq' => 'weekly', 'priority' => 0.8],
            ['url' => '/location', 'freq' => 'monthly', 'priority' => 0.7],
            ['url' => '/services', 'freq' => 'monthly', 'priority' => 0.7],
            ['url' => '/amenities', 'freq' => 'monthly', 'priority' => 0.7],
            ['url' => '/house-rules', 'freq' => 'monthly', 'priority' => 0.6],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->urlEntry(
                $siteUrl . $page['url'],
                $currentDate,
                $page['freq'],
                $page['priority']
            );
        }

        // Room Type pages
        $roomTypes = RoomType::where('is_active', true)->get();
        foreach ($roomTypes as $roomType) {
            $sitemap .= $this->urlEntry(
                $siteUrl . '/rooms/' . $roomType->slug,
                $roomType->updated_at ?? $roomType->created_at,
                'weekly',
                0.8
            );
        }

        // Amenity pages
        $amenities = Amenity::where('is_active', true)->get();
        foreach ($amenities as $amenity) {
            $sitemap .= $this->urlEntry(
                $siteUrl . '/amenities/' . $amenity->id,
                $amenity->updated_at,
                'monthly',
                0.6
            );
        }

        // Service pages
        $services = Service::where('is_active', true)->get();
        foreach ($services as $service) {
            $sitemap .= $this->urlEntry(
                $siteUrl . '/services/' . $service->id,
                $service->updated_at,
                'monthly',
                0.6
            );
        }

        // Gallery pages
        $galleries = GalleryImage::where('is_active', true)
            ->whereNotNull('imageable_type')
            ->distinct('imageable_id')
            ->get();

        foreach ($galleries as $gallery) {
            if (!empty($gallery->imageable_id)) {
                $sitemap .= $this->urlEntry(
                    $siteUrl . '/gallery/' . $gallery->imageable_id,
                    $gallery->updated_at,
                    'monthly',
                    0.6
                );
            }
        }

        // House Rules page (if individual rules have URLs)
        $houseRules = HouseRule::where('is_active', true)->get();
        foreach ($houseRules as $rule) {
            $sitemap .= $this->urlEntry(
                $siteUrl . '/house-rules/' . $rule->id,
                $rule->updated_at,
                'monthly',
                0.5
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 24 hours
    }

    /**
     * Generate a single URL entry for the sitemap.
     *
     * @param string $url
     * @param mixed $lastmod
     * @param string $changefreq
     * @param float $priority
     * @return string
     */
    private function urlEntry(string $url, $lastmod, string $changefreq = 'weekly', float $priority = 0.5): string
    {
        $lastmodDate = $lastmod instanceof \DateTime || $lastmod instanceof \Illuminate\Support\Carbon
            ? $lastmod->toDateString()
            : now()->toDateString();

        return "  <url>\n"
            . "    <loc>" . htmlspecialchars($url) . "</loc>\n"
            . "    <lastmod>{$lastmodDate}</lastmod>\n"
            . "    <changefreq>{$changefreq}</changefreq>\n"
            . "    <priority>{$priority}</priority>\n"
            . "  </url>\n";
    }
}
