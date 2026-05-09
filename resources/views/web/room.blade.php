@extends('layouts.app')

@section('title', ($roomType->name ?? 'Luxury Suite') . ' - DwellCasa')

@push('head')
<style>
    /* Strikethrough ONLY for fully booked dates in flatpickr */
    .flatpickr-day.fully-booked-date {
        text-decoration: line-through;
        color: #cbd5e1 !important;
    }
</style>
@endpush

@section('content')
@php
$galleryImages = $roomType->galleryImages ?? collect();
$imagesForLightbox = $galleryImages->count() > 0
? $galleryImages->map(fn($img) => ['url' => asset('storage/' . $img->filename), 'caption' => $img->caption, 'alt' => $img->alt_text])->toArray()
: [
['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=1200', 'caption' => '', 'alt' => 'Room View'],
['url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&q=80&w=600', 'caption' => '', 'alt' => 'Bathroom'],
['url' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=600', 'caption' => '', 'alt' => 'Details'],
];

$image1 = $imagesForLightbox[0]['url'] ?? '';
$image2 = $imagesForLightbox[1]['url'] ?? $imagesForLightbox[0]['url'];
$image3 = $imagesForLightbox[2]['url'] ?? $imagesForLightbox[0]['url'];
@endphp
<!-- Hero Image Section -->
<section class="mt-4 relative h-[60vh] min-h-[400px] flex items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <img src="{{ $roomType->thumbnail ? asset('storage/' . $roomType->thumbnail) : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=1920' }}"
            class="w-full h-full object-cover opacity-60" alt="{{ $roomType->name ?? 'Room Hero' }}">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-900/60"></div>
    </div>

    <div class="relative z-10 text-center text-white px-6 mt-16" data-aos="fade-up">
        <span class="font-serif uppercase tracking-[0.2em] text-sm mb-4 block font-medium">DwellCasa Signature</span>
        <h1 class="font-serif text-5xl md:text-7xl font-bold italic mb-4">{{ $roomType->name ?? 'Executive Suite' }}</h1>
        <div class="flex items-center justify-center gap-4 text-sm font-light tracking-widest uppercase">
            <span>{{ $roomType->size_sqft ?? '450' }} sq ft</span>
            <span class="text-primary font-bold">•</span>
            <span>Up to {{ $roomType->max_occupancy ?? '2' }} Guests</span>
        </div>
    </div>
</section>

<section class="py-16 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Image Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-16" data-aos="fade-up">
            <div class="md:col-span-2 aspect-[16/9] md:aspect-[2/1] rounded-[2rem] overflow-hidden cursor-pointer group shadow-sm" onclick="openLightbox(0)">
                <img src="{{ $image1 }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Room View">
            </div>
            <div class="grid grid-cols-2 md:grid-cols-1 gap-4">
                <div class="aspect-square md:aspect-auto md:h-full rounded-[2rem] overflow-hidden cursor-pointer group shadow-sm" onclick="openLightbox(1)">
                    <img src="{{ $image2 }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Bathroom">
                </div>
                <div class="aspect-square md:aspect-auto md:h-full rounded-[2rem] overflow-hidden cursor-pointer group relative shadow-sm" onclick="openLightbox(0)">
                    <img src="{{ $image3 }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Details">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center transition-colors group-hover:bg-black/50">
                        <span class="text-white font-semibold tracking-wide flex items-center gap-2">View All Photos <span>→</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Description -->
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-6">About this Space</h2>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        {{ $roomType->description ?? "Experience unparalleled luxury and comfort in our meticulously designed suite. Blending contemporary elegance with subtle traditional touches, this room offers a serene sanctuary above the bustling city. Large windows frame stunning views, while the plush bedding ensures a restful night's sleep." }}
                    </p>
                </div>

                <!-- Amenities -->
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-6">What this room offers</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        @forelse($roomType->amenities ?? [] as $amenity)
                        @if ($amenity->is_active == 1)
                        <div class="flex items-center gap-3 text-slate-700">
                            <span class="text-2xl text-black">{!! $amenity->icon ?: '✨' !!}</span>
                            <span class="font-medium">{{ $amenity->name }}</span>
                        </div>
                        @endif
                        @empty
                        <p class="text-slate-500 italic col-span-full">No specific amenities listed for this room.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar / Sticky Booking Card -->
            <div class="lg:col-span-1" data-aos="fade-left" data-aos-delay="200">
                <div class="sticky top-32 bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100">
                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Starting From</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-serif font-bold text-slate-900" id="bw-rate-display">
                                Rs. {{ number_format($roomType->price_per_night ?? 15000, 0) }}
                            </span>
                            <span class="text-slate-500 font-medium">/night</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1 font-medium" id="bw-nights-label"></p>
                    </div>

                    <!-- Date + Guest Fields -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden mb-4">
                        <div class="grid grid-cols-2 divide-x divide-slate-200">
                            <div class="p-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Check-in</p>
                                <input id="bw-checkin" type="text" placeholder="MM/DD/YYYY" readonly
                                    class="text-sm text-slate-800 w-full outline-none cursor-pointer bg-transparent placeholder-slate-300">
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Checkout</p>
                                <input id="bw-checkout" type="text" placeholder="MM/DD/YYYY" readonly
                                    class="text-sm text-slate-800 w-full outline-none cursor-pointer bg-transparent placeholder-slate-300">
                            </div>
                        </div>
                        <div class="border-t border-slate-200 p-3 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Guests</p>
                                <select id="bw-guests" class="text-sm text-slate-800 bg-transparent outline-none cursor-pointer">
                                    @for ($i = 1; $i <= ($roomType->max_occupancy ?? 4); $i++)
                                        <option value="{{ $i }}">{{ $i }} guest{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div id="bw-summary" class="hidden bg-slate-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span id="bw-rate-label"></span>
                            <span id="bw-subtotal"></span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-900 pt-2 border-t border-slate-200">
                            <span>Total</span>
                            <span id="bw-total"></span>
                        </div>
                    </div>

                    <!-- Cancellation note -->
                    <p id="bw-cancel-msg" class="hidden text-xs text-slate-500 text-center mb-4">
                        Free cancellation before <span id="bw-cancel-date" class="underline"></span>
                    </p>

                    <!-- Hidden form -->
                    <form id="bw-form" action="{{ route('booking.create', $location->slug) }}" method="GET">
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                        <input type="hidden" name="stay_type" value="short_term">
                        <input type="hidden" name="check_in_date" id="bw-form-checkin">
                        <input type="hidden" name="check_out_date" id="bw-form-checkout">
                        <input type="hidden" name="num_guests" id="bw-form-guests">

                        <button type="submit" id="bw-btn"
                            class="hidden w-full bg-primary text-white text-center px-6 py-4 rounded-xl font-bold tracking-wide 
                       transition-all transform hover:-translate-y-1
                       hover:bg-primary-dark hover:shadow-lg">
                            Reserve Now
                        </button>
                    </form>

                    <p class="text-center text-xs text-slate-400 mt-4 font-medium tracking-wide">
                        You won't be charged yet
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

@php
$reviews = \App\Models\Review::where('type', 'room_type')
->where('room_type_id', $roomType->id)
->where('status', 'approved')
->orderByDesc('rating')
->latest()
->get();
@endphp

@if($reviews->count() > 0)
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="uppercase tracking-[0.2em] text-xs text-primary font-bold mb-3 block">Guest Feedback</span>
            <h2 class="font-serif font-bold text-3xl md:text-4xl text-slate-900 italic">Reviews for {{ $roomType->name }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="reviews-grid">
            @foreach($reviews as $review)
            <div class="bg-slate-50 p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between transition-transform duration-300 hover:-translate-y-1 {{ $loop->index >= 6 ? 'hidden review-hidden' : '' }}" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 5) * 100 }}">
                <div>
                    <div class="flex items-center mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-2xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                        @endfor
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">"{{ $review->body }}"</p>
                </div>
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200/50">
                    @if($review->avatar)
                    <img src="{{ asset('storage/' . $review->avatar) }}" alt="{{ $review->name }}"
                         class="w-12 h-12 rounded-full object-cover shadow-inner flex-shrink-0">
                    @else
                    <div class="w-12 h-12 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-lg shadow-inner flex-shrink-0">
                        {{ substr($review->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <p class="font-bold text-sm text-slate-900">{{ $review->name }}</p>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Verified Guest &bull; {{ $review->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($reviews->count() > 6)
        <div class="text-center mt-10">
            <button type="button" id="show-more-reviews"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium hover:border-primary hover:text-primary transition-colors cursor-pointer">
                Show more reviews
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
        </div>
        @endif
    </div>
</section>
@endif

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <!-- Controls -->
    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <div id="lightbox-counter" class="text-white font-medium text-sm">1 / 3</div>
        <button type="button" onclick="closeLightbox()" class="text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Image Container -->
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center p-4 md:p-12 overflow-hidden touch-pan-y gap-4 md:gap-8">
        <button type="button" onclick="prevImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">

        <button type="button" onclick="nextImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Caption -->
    <div class="absolute bottom-0 left-0 w-full p-6 text-center z-10 bg-gradient-to-t from-black/80 to-transparent">
        <h3 id="lightbox-caption" class="text-white text-lg font-serif italic mb-1"></h3>
    </div>
</div>
@push('scripts')
<script>
    (function() {
        const RATE = {{ $roomType -> price_per_night ?? 15000 }};
        const BOOKED = @json($bookedDates ?? []);

        // Lightbox Logic
        const galleryImagesData = @json($imagesForLightbox);
        let currentLightboxIndex = 0;

        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxCounter = document.getElementById('lightbox-counter');

        window.openLightbox = function(index) {
            currentLightboxIndex = index;
            if (currentLightboxIndex >= galleryImagesData.length) {
                currentLightboxIndex = 0;
            }
            updateLightbox();
            if (lightbox) {
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
                setTimeout(() => {
                    lightbox.classList.remove('opacity-0');
                }, 10);
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeLightbox = function() {
            if (lightbox) {
                lightbox.classList.add('opacity-0');
                setTimeout(() => {
                    lightbox.classList.add('hidden');
                    lightbox.classList.remove('flex');
                    document.body.style.overflow = '';
                }, 300);
            }
        };

        window.nextImage = function(e) {
            if (e) e.stopPropagation();
            if (!galleryImagesData || galleryImagesData.length === 0) return;
            currentLightboxIndex = (currentLightboxIndex + 1) % galleryImagesData.length;
            animateSlide('right');
        };

        window.prevImage = function(e) {
            if (e) e.stopPropagation();
            if (!galleryImagesData || galleryImagesData.length === 0) return;
            currentLightboxIndex = (currentLightboxIndex - 1 + galleryImagesData.length) % galleryImagesData.length;
            animateSlide('left');
        };

        function updateLightbox() {
            if (!galleryImagesData || galleryImagesData.length === 0) return;
            const img = galleryImagesData[currentLightboxIndex];

            if (lightboxImg) {
                lightboxImg.src = img.url;
                lightboxImg.alt = img.alt || 'Gallery Image';
            }
            if (lightboxCaption) {
                lightboxCaption.textContent = img.caption || img.alt || '';
            }
            if (lightboxCounter) {
                lightboxCounter.textContent = `${currentLightboxIndex + 1} / ${galleryImagesData.length}`;
            }
        }

        function animateSlide(direction) {
            if (!lightboxImg) return;
            lightboxImg.style.transform = `translateX(${direction === 'right' ? '20px' : '-20px'}) scale(0.98)`;
            lightboxImg.style.opacity = '0.5';

            setTimeout(() => {
                updateLightbox();
                lightboxImg.style.transform = 'translateX(0) scale(1)';
                lightboxImg.style.opacity = '1';
            }, 150);
        }

        document.addEventListener('keydown', (e) => {
            if (lightbox && !lightbox.classList.contains('hidden')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowRight') nextImage();
                if (e.key === 'ArrowLeft') prevImage();
            }
        });

        // Touch Swipe Logic
        let touchStartX = 0;
        let touchEndX = 0;
        const imgContainer = document.getElementById('lightbox-img-container');

        if (imgContainer) {
            imgContainer.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, {
                passive: true
            });

            imgContainer.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, {
                passive: true
            });
        }

        function handleSwipe() {
            const threshold = 50;
            if (touchEndX < touchStartX - threshold) nextImage();
            if (touchEndX > touchStartX + threshold) prevImage();
        }

        let checkInDate = null;
        let checkOutDate = null;
        let checkoutPicker = null;

        const checkinInput = document.getElementById('bw-checkin');
        const checkoutInput = document.getElementById('bw-checkout');
        const btn = document.getElementById('bw-btn');
        const summary = document.getElementById('bw-summary');
        const rateLabel = document.getElementById('bw-rate-label');
        const subtotalEl = document.getElementById('bw-subtotal');
        const totalEl = document.getElementById('bw-total');
        const nightsLabel = document.getElementById('bw-nights-label');
        const cancelMsg = document.getElementById('bw-cancel-msg');
        const cancelDate = document.getElementById('bw-cancel-date');

        function formatShort(d) {
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
        }

        function toLocalYMD(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function updateSummary() {
            if (!checkInDate || !checkOutDate) {
                summary.classList.add('hidden');
                cancelMsg.classList.add('hidden');
                nightsLabel.textContent = '';
                btn.classList.add('hidden');
                btn.classList.remove('block');
                return;
            }

            const nights = Math.round((checkOutDate - checkInDate) / 86400000);
            const subtotal = nights * RATE;

            rateLabel.textContent = 'Rs. ' + RATE.toLocaleString() + ' x ' + nights + ' night' + (nights !== 1 ? 's' : '');
            subtotalEl.textContent = 'Rs. ' + subtotal.toLocaleString();
            totalEl.textContent = 'Rs. ' + subtotal.toLocaleString();
            nightsLabel.textContent = nights + ' night' + (nights !== 1 ? 's' : '') + ' total';

            const cancelD = new Date(checkInDate);
            cancelD.setDate(cancelD.getDate() - 1);
            cancelDate.textContent = formatShort(cancelD);

            document.getElementById('bw-form-checkin').value = toLocalYMD(checkInDate);
            document.getElementById('bw-form-checkout').value = toLocalYMD(checkOutDate);
            document.getElementById('bw-form-guests').value = document.getElementById('bw-guests').value;

            summary.classList.remove('hidden');
            cancelMsg.classList.remove('hidden');
            btn.classList.remove('hidden');
            btn.classList.add('block');
        }

        flatpickr(checkinInput, {
            minDate: 'today',
            dateFormat: 'm/d/Y',
            disable: [
                function(date) {
                    return BOOKED.includes(toLocalYMD(date));
                }
            ],
            disableMobile: true,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const localDate = toLocalYMD(dayElem.dateObj);
                if (BOOKED.includes(localDate)) {
                    dayElem.classList.add('fully-booked-date');
                }
            },
            onChange: function(selected) {
                checkInDate = selected[0] || null;
                if (checkInDate && checkOutDate && checkOutDate <= checkInDate) {
                    checkOutDate = null;
                    checkoutPicker.clear();
                }
                if (checkoutPicker) {
                    checkoutPicker.set('minDate', checkInDate ?
                        new Date(checkInDate.getTime() + 86400000) :
                        'today');

                    let maxDate = null;
                    if (checkInDate) {
                        const checkInStr = toLocalYMD(checkInDate);
                        for (let i = 0; i < BOOKED.length; i++) {
                            if (BOOKED[i] >= checkInStr) {
                                maxDate = BOOKED[i];
                                break;
                            }
                        }
                    }
                    checkoutPicker.set('maxDate', maxDate);

                    if (maxDate && checkOutDate) {
                        if (toLocalYMD(checkOutDate) > maxDate) {
                            checkOutDate = null;
                            checkoutPicker.clear();
                        }
                    }
                }
                updateSummary();
            }
        });

        checkoutPicker = flatpickr(checkoutInput, {
            minDate: 'today',
            dateFormat: 'm/d/Y',
            disable: [
                function(date) {
                    return BOOKED.includes(toLocalYMD(date));
                }
            ],
            disableMobile: true,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const localDate = toLocalYMD(dayElem.dateObj);
                if (BOOKED.includes(localDate)) {
                    dayElem.classList.add('fully-booked-date');
                }
            },
            onChange: function(selected) {
                checkOutDate = selected[0] || null;
                updateSummary();
            }
        });

        document.getElementById('bw-guests').addEventListener('change', updateSummary);
    })();

    // Show more reviews
    document.getElementById('show-more-reviews')?.addEventListener('click', function () {
        document.querySelectorAll('.review-hidden').forEach(card => card.classList.remove('hidden'));
        this.closest('div').remove();
    });
</script>
@endpush
@endsection