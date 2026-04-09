{{-- SEO Meta Tags Partial --}}
{{-- Outputs title, meta description, keywords, Open Graph tags, Twitter Card tags, and canonical link --}}

@props([
    'seo' => [],
])

@php
    $seoData = $seo ?? [];
    $title = $seoData['title'] ?? 'DwellCasa - Luxury Hotel Booking';
    $description = $seoData['description'] ?? 'Experience luxury accommodations and exceptional hospitality.';
    $keywords = $seoData['keywords'] ?? 'hotel, booking, luxury, accommodation';
    $ogImage = $seoData['og:image'] ?? asset('images/og-image.jpg');
    $ogUrl = $seoData['og:url'] ?? config('app.url');
    $ogTitle = $seoData['og:title'] ?? $title;
    $ogDescription = $seoData['og:description'] ?? $description;
    $twitterTitle = $seoData['twitter:title'] ?? $title;
    $twitterDescription = $seoData['twitter:description'] ?? $description;
    $twitterImage = $seoData['twitter:image'] ?? $ogImage;
    $twitterCard = $seoData['twitter:card'] ?? 'summary_large_image';
    $canonical = $seoData['canonical'] ?? $ogUrl;
@endphp

{{-- Title --}}
<title>{{ $title }}</title>

{{-- Meta Description --}}
<meta name="description" content="{{ Illuminate\Support\Str::limit($description, 160) }}">

{{-- Meta Keywords --}}
<meta name="keywords" content="{{ $keywords }}">

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ Illuminate\Support\Str::limit($ogDescription, 160) }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:type" content="{{ $seoData['og:type'] ?? 'website' }}">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ Illuminate\Support\Str::limit($twitterDescription, 160) }}">
<meta name="twitter:image" content="{{ $twitterImage }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Additional SEO Enhancements --}}
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">
<meta name="author" content="DwellCasa">

{{-- Apple & Mobile Enhancements --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

{{-- Preload Critical Resources --}}
<link rel="preload" as="font" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" crossorigin>
