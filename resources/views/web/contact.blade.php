@extends('layouts.app')

@section('title', 'Contact Us - DwellCasa')

@section('content')
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl !font-sans font-bold text-slate-900 mb-4">Contact Us</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                {{ $webInfo->contact_sub_heading }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white p-10 rounded-2xl shadow-lg border border-slate-100">
                <h2 class="text-3xl !font-sans font-bold text-slate-900 mb-8">Send us a Message</h2>
                <form id="contact-form" action="#" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Inquiry Type:</label>
                        <select name="inquiry_type" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="general">General Inquiry</option>
                            <option value="booking">Booking & Reservation</option>
                            <option value="amenities">Amenities & Facilities</option>
                            <option value="pricing">Pricing & Rates</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                        <textarea name="message" rows="5" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required></textarea>
                    </div>
                    <button type="submit" class="w-full cursor-pointer bg-primary text-white px-6 py-4 rounded-lg font-semibold hover:shadow-lg hover:bg-primary-dark transition-all">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div>
                <div class="bg-white p-10 rounded-2xl shadow-lg border border-slate-100 mb-8">
                    <h2 class="text-3xl !font-sans font-bold text-slate-900 mb-8">Get in Touch</h2>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">📍</span>
                            </div>
                            <div>
                                <h3 class="!font-sans font-bold text-slate-900 text-lg">Address</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">📞</span>
                            </div>
                            <div>
                                <h3 class="!font-sans font-bold text-slate-900 text-lg">Phone</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_phone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">✉️</span>
                            </div>
                            <div>
                                <h3 class="!font-sans font-bold text-slate-900 text-lg">Email</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">🕒</span>
                            </div>
                            <div>
                                <h3 class="!font-sans font-bold text-slate-900 text-lg">Business Hours</h3>
                                <p class="text-slate-700 mt-1">Check-in: {{ \Carbon\Carbon::parse($webInfo->check_in)->format('g:i A') }}<br>Check-out: {{ \Carbon\Carbon::parse($webInfo->check_out)->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                @if($webInfo->map_lat && $webInfo->map_lng)
                <div id="property-map" class="h-72 rounded-2xl overflow-hidden shadow-lg border border-slate-200"></div>
                @else
                <div class="bg-gradient-to-br from-slate-200 to-slate-300 h-72 rounded-2xl flex items-center justify-center shadow-lg border border-slate-200">
                    <span class="text-slate-600 font-medium">Interactive Map</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('head')
@if($webInfo->map_lat && $webInfo->map_lng)
<script>
    window.__mapLat     = {{ (float) $webInfo->map_lat }};
    window.__mapLng     = {{ (float) $webInfo->map_lng }};
    window.__mapName    = @json($location->name ?? 'DwellCasa');
    window.__mapAddress = @json($webInfo->contact_address ?? '');
    window.__mapPhone   = @json($webInfo->contact_phone ?? '');

    window.initPropertyMap = function() {
        const center = { lat: window.__mapLat, lng: window.__mapLng };

        const mapStyles = [
            { elementType: 'geometry',            stylers: [{ color: '#f5f4f0' }] },
            { elementType: 'labels.text.fill',    stylers: [{ color: '#6b7280' }] },
            { elementType: 'labels.text.stroke',  stylers: [{ color: '#f5f4f0' }] },
            { featureType: 'road',              elementType: 'geometry',         stylers: [{ color: '#ffffff' }] },
            { featureType: 'road',              elementType: 'labels.text.fill', stylers: [{ color: '#9ca3af' }] },
            { featureType: 'road.highway',      elementType: 'geometry',         stylers: [{ color: '#e5e1db' }] },
            { featureType: 'water',             elementType: 'geometry',         stylers: [{ color: '#c8d9e8' }] },
            { featureType: 'water',             elementType: 'labels.text.fill', stylers: [{ color: '#9ca3af' }] },
            { featureType: 'poi',               elementType: 'geometry',         stylers: [{ color: '#ede8e0' }] },
            { featureType: 'poi.park',          elementType: 'geometry',         stylers: [{ color: '#dce8d6' }] },
            { featureType: 'poi',               elementType: 'labels',           stylers: [{ visibility: 'off' }] },
            { featureType: 'transit',           elementType: 'geometry',         stylers: [{ color: '#e8e4de' }] },
            { featureType: 'administrative',    elementType: 'geometry.stroke',  stylers: [{ color: '#d1c9be' }] },
            { featureType: 'administrative',    elementType: 'labels.text.fill', stylers: [{ color: '#A89070' }] },
        ];

        const map = new google.maps.Map(document.getElementById('property-map'), {
            center,
            zoom: 15,
            styles: mapStyles,
            disableDefaultUI: true,
            zoomControl: true,
            zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_BOTTOM },
        });

        const markerSvg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="44" viewBox="0 0 36 44">
                <path d="M18 0C8.06 0 0 8.06 0 18c0 13.5 18 26 18 26S36 31.5 36 18C36 8.06 27.94 0 18 0z"
                      fill="#A89070"/>
                <circle cx="18" cy="18" r="7" fill="#ffffff"/>
            </svg>`;

        const marker = new google.maps.Marker({
            position: center,
            map,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(markerSvg),
                scaledSize: new google.maps.Size(36, 44),
                anchor: new google.maps.Point(18, 44),
            },
            title: window.__mapName,
        });

        const infoContent = `
            <div style="font-family:sans-serif;padding:4px 2px;max-width:220px;">
                <p style="font-weight:700;color:#1e293b;margin:0 0 4px;">${window.__mapName}</p>
                ${window.__mapAddress ? `<p style="color:#475569;font-size:13px;margin:0 0 3px;">${window.__mapAddress}</p>` : ''}
                ${window.__mapPhone   ? `<p style="color:#475569;font-size:13px;margin:0;">${window.__mapPhone}</p>` : ''}
            </div>`;

        const infoWindow = new google.maps.InfoWindow({ content: infoContent });

        marker.addListener('click', () => infoWindow.open({ anchor: marker, map }));
        infoWindow.open({ anchor: marker, map });
    };
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initPropertyMap"
    async defer></script>
@endif
@endpush

@push('scripts')
<script>
    document.getElementById('contact-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending...';

            const response = await fetch('/api/inquiries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Thank you! Your message has been sent successfully.');
                form.reset();
            } else {
                const error = await response.json();
                let errorMessage = 'Error sending message: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while sending your message.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Send Message';
        }
    });
</script>
@endpush
@endsection