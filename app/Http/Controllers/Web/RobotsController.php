<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt file.
     * Allows all crawlers, disallows /admin/ path, and includes sitemap reference.
     *
     * @return Response
     */
    public function index(): Response
    {
        $siteUrl = config('app.url');
        $sitemapUrl = $siteUrl . '/sitemap.xml';

        $robots = "# DwellCasa Robots.txt\n";
        $robots .= "# Generated dynamically for SEO purposes\n\n";

        // Default rules for all user agents
        $robots .= "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /*.json$\n";
        $robots .= "Disallow: /search\n";
        $robots .= "Disallow: /cart\n";
        $robots .= "Disallow: /checkout\n";
        $robots .= "\n";

        // Specific rules for Googlebot
        $robots .= "User-agent: Googlebot\n";
        $robots .= "Allow: /\n";
        $robots .= "Crawl-delay: 0\n";
        $robots .= "\n";

        // Specific rules for Bingbot
        $robots .= "User-agent: Bingbot\n";
        $robots .= "Allow: /\n";
        $robots .= "Crawl-delay: 1\n";
        $robots .= "\n";

        // Sitemap reference
        $robots .= "Sitemap: {$sitemapUrl}\n";

        return response($robots, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 24 hours
    }
}
