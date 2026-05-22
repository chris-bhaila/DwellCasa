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
                <!-- Success state (hidden until form is submitted successfully) -->
                <div id="contact-success" class="hidden flex flex-col items-center justify-center text-center h-full py-10">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6" style="background-color:#A89070/10;background-color:rgba(168,144,112,0.12);">
                        <svg class="w-10 h-10" style="color:#A89070" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-3xl !font-sans font-bold text-slate-900 mb-3">Message Sent!</h2>
                    <p class="text-slate-600 text-lg mb-2">Thank you for reaching out to us.</p>
                    <p class="text-slate-500 text-sm mb-8">We'll get back to you as soon as possible.</p>
                    <button onclick="document.getElementById('contact-success').classList.add('hidden');document.getElementById('contact-form-wrap').classList.remove('hidden');"
                        class="text-sm font-medium underline underline-offset-2 cursor-pointer" style="color:#A89070">
                        Send another message
                    </button>
                </div>

                <!-- Form -->
                <div id="contact-form-wrap">
                    <h2 class="text-3xl !font-sans font-bold text-slate-900 mb-8">Send us a Message</h2>

                    <!-- Error banner -->
                    <div id="contact-error" class="hidden mb-6 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="contact-error-text"></span>
                    </div>

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
                            <select name="inquiry_type" class="w-full px-4 py-3 cursor-pointer border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
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
                        <button type="submit" id="contact-submit-btn" class="w-full cursor-pointer bg-primary text-white px-6 py-4 rounded-lg font-semibold hover:shadow-lg hover:bg-primary-dark transition-all">
                            Send Message
                        </button>
                    </form>
                </div>
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
                                <p class="text-slate-700 mt-1">{{ implode(', ', array_filter((array) ($webInfo->contact_phone ?? []))) }}</p>
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
    window.__mapLat = {{ (float) $webInfo->map_lat }};
    window.__mapLng = {{ (float) $webInfo->map_lng }};
    window.__mapName = @json($location->name ?? 'DwellCasa');
    window.__mapAddress = @json($webInfo->contact_address ?? '');
    window.__mapPhone = @json(implode(', ', array_filter((array) ($webInfo->contact_phone ?? []))));

    async function initPropertyMap() {
        const mapEl = document.getElementById('property-map');
        if (!mapEl) return;

        const center = { lat: window.__mapLat, lng: window.__mapLng };

        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

        const map = new Map(mapEl, {
            center,
            zoom: 15,
            mapId: "{{ config('services.google_maps.map_id') }}",
            disableDefaultUI: true,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
        });

        const pin = new PinElement({
            background: "#A89070",
            borderColor: "#8a7460",
            glyphColor: "#ffffff",
        });

        const marker = new AdvancedMarkerElement({
            position: center,
            map,
            title: window.__mapName,
            content: pin.element,
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
    }
</script>
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}),
            r = new Set,
            e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("language", "en");
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a);
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n));
    })({
        key: "{{ config('services.google_maps.key') }}",
        v: "weekly"
    });

    document.addEventListener('DOMContentLoaded', initPropertyMap);
</script>
@endif
@endpush

@push('scripts')
<script>
    const contactForm = document.getElementById('contact-form');
    const contactWrap = document.getElementById('contact-form-wrap');
    const contactSuccess = document.getElementById('contact-success');
    const contactError = document.getElementById('contact-error');
    const contactErrorTxt = document.getElementById('contact-error-text');
    const submitBtn = document.getElementById('contact-submit-btn');

    function showError(message) {
        contactErrorTxt.textContent = message;
        contactError.classList.remove('hidden');
        contactError.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        contactError.classList.add('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

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
                contactForm.reset();
                contactWrap.classList.add('hidden');
                contactSuccess.classList.remove('hidden');
                contactSuccess.classList.add('flex');
            } else {
                const error = await response.json();
                let msg = error.message || 'Something went wrong. Please try again.';
                if (error.errors) {
                    msg = Object.values(error.errors).flat().join(' ');
                }
                showError(msg);
            }
        } catch (err) {
            console.error(err);
            showError('An error occurred while sending your message. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        }
    });
</script>
@endpush
@endsection