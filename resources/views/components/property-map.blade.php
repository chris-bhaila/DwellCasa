<!-- Property Map Component -->
<!-- Renders a responsive Google Maps iframe from property_settings embed URL -->
@props([
    'class' => 'w-full',
    'height' => 'h-96',
])

@php
    use App\Models\PropertySetting;
    
    $embedUrl = PropertySetting::where('key', 'google_maps_embed_url')->value('value');
@endphp

@if ($embedUrl)
    <div class="{{ $class }}">
        <iframe
            class="w-full {{ $height }}"
            style="border: 0; border-radius: 0.5rem;"
            src="{{ $embedUrl }}"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
@else
    <div class="flex items-center justify-center {{ $class }} {{ $height }} bg-gray-100 rounded">
        <p class="text-gray-500">Map configuration not available</p>
    </div>
@endif
