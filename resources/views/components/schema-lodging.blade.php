{{-- JSON-LD Structured Data Component - LodgingBusiness Schema --}}
{{-- Renders structured data for Google, Bing, and other search engines --}}

@props([
    'name' => null,
    'description' => null,
    'image' => null,
    'url' => null,
    'telephone' => null,
    'email' => null,
    'address' => null,
    'latitude' => null,
    'longitude' => null,
    'checkInTime' => '14:00',
    'checkOutTime' => '11:00',
    'rating' => null,
    'reviewCount' => null,
])

@php
    use App\Models\PropertySetting;
    use Illuminate\Support\Str;

    // Get default values from settings or use provided props
    $siteName = $name ?? PropertySetting::where('key', 'seo_site_name')->value('value') ?? 'DwellCasa';
    $siteDescription = $description ?? PropertySetting::where('key', 'seo_default_description')->value('value') ?? 'Experience luxury accommodations and exceptional hospitality.';
    $siteImage = $image ?? PropertySetting::where('key', 'seo_og_image')->value('value') ?? asset('images/og-image.jpg');
    $siteUrl = $url ?? config('app.url');
    $siteTelephone = $telephone ?? config('app.phone', '+1-800-HOTEL');
    $siteEmail = $email ?? config('app.email', 'info@dwellcasa.com');
    $siteAddress = $address ?? 'Luxury Hotel Address';
    $siteLat = $latitude ?? 40.7128;
    $siteLon = $longitude ?? -74.0060;

    // Build the JSON-LD schema
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LodgingBusiness',
        'name' => $siteName,
        'description' => Str::limit($siteDescription, 200),
        'image' => $siteImage,
        'url' => $siteUrl,
        'telephone' => $siteTelephone,
        'email' => $siteEmail,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $siteAddress,
            'addressCountry' => 'US',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $siteLat,
            'longitude' => $siteLon,
        ],
        'checkinTime' => $checkInTime,
        'checkoutTime' => $checkOutTime,
        'priceRange' => '$$$$',
        'amenityFeature' => [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Luxury Rooms'],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Fine Dining'],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Concierge Service'],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Free WiFi'],
        ],
    ];

    // Add rating if provided
    if ($rating && $reviewCount) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $rating,
            'reviewCount' => $reviewCount,
        ];
    }

    // Add contact point
    $schema['contactPoint'] = [
        '@type' => 'ContactPoint',
        'contactType' => 'Customer Service',
        'telephone' => $siteTelephone,
        'email' => $siteEmail,
    ];

@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
