@extends('layouts.app')

@section('title', 'DwellCasa - Luxury Stays in Lalitpur')

@section('content')

@push('head')
<style>
    /* Typography & Core Styling */
    .font-serif {
        font-family: 'Cormorant Garamond', serif;
    }

    .font-sans {
        font-family: 'DM Sans', sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background-color: #fbfbf9;
        overflow-x: hidden;
    }

    /* Glassmorphism Effect */
    .glass {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Hero Animation */
    .hero-zoom {
        animation: slowZoom 20s infinite alternate ease-in-out;
    }

    @keyframes slowZoom {
        from {
            transform: scale(1);
        }

        to {
            transform: scale(1.1);
        }
    }

    /* Custom Scrollbar for Luxury Feel */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 10px;
    }

    /* Remove default date styling for cleaner UI */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }

    /* Reviews Marquee */
    .marquee-track {
        flex-shrink: 0;
        display: flex;
        min-width: 100%;
        animation: scrollLeft 40s linear infinite;
    }

    .marquee-container:hover .marquee-track {
        animation-play-state: paused;
    }

    @keyframes scrollLeft {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-100%);
        }
    }
</style>
@endpush

<main>

    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0">
            <img src="{{ $webInfo->homepage_main_image ? asset('storage/' . $webInfo->homepage_main_image) : 'https://upload.wikimedia.org/wikipedia/commons/6/68/Pashupatinaath0587.JPG' }}"
                class="hero-zoom w-full h-full object-cover opacity-50" alt="DwellCasa">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-900/60"></div>
        </div>

        <div class="relative z-10 text-center text-white px-6 max-w-5xl" data-aos="fade-up" data-aos-duration="1200">
            <span class="font-serif uppercase tracking-[0.1em] text-xs md:text-xl mb-6 block font-medium opacity-90">{{ $webInfo->front_page_sub_heading_1 }}</span>
            <h1 class="font-serif text-5xl font-extrabold md:text-8xl mb-8 italic">{{ $webInfo->front_page_main_heading }}</h1>
            <p class="text-base md:text-xl font-light max-w-2xl mx-auto mb-12 leading-relaxed opacity-80">
                {{ $webInfo->front_page_sub_heading_2 }}
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="#rooms" class="w-full sm:w-auto bg-white text-slate-900 px-10 py-4 rounded-full font-bold hover:bg-slate-100 transition-all transform hover:-translate-y-1 shadow-xl">
                    Explore Suites
                </a>
                <!-- <a href="#booking-form" class="w-full sm:w-auto border border-white/40 backdrop-blur-md px-10 py-4 rounded-full font-semibold hover:bg-white/20 transition-all">
                    Quick Booking
                </a> -->
            </div>
        </div>
    </section>

    <section id="booking-form" class="relative z-30 -mt-10 md:-mt-14 px-4 scroll-mt-24">
        <div class="max-w-6xl mx-auto">
            <div class="glass shadow-2xl rounded-3xl md:rounded-full p-2 md:p-3" data-aos="zoom-in" data-aos-delay="200">
                @if(isset($location))
                <form action="{{ route('booking.create', $location->slug) }}" method="GET" class="flex flex-col md:flex-row items-center gap-1">
                    <div class="w-full md:flex-1 px-8 py-4 border-b md:border-b-0 md:border-r border-slate-200/50">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Room Type</label>
                        <select id="loc-roomtype" name="room_type_id" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800 appearance-none cursor-pointer">
                            @foreach($featuredRoomTypes as $roomType)
                            <option value="{{ $roomType->id }}" data-max="{{ $roomType->max_occupancy }}">{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:flex-1 px-8 py-4 border-b md:border-b-0 md:border-r border-slate-200/50">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Check In</label>
                        <input type="text" id="loc-checkin" name="check_in_date" placeholder="Select date" readonly
                            class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800 cursor-pointer placeholder-slate-300">
                    </div>
                    <div class="w-full md:flex-1 px-8 py-4 border-b md:border-b-0 md:border-r border-slate-200/50">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Check Out</label>
                        <input type="text" id="loc-checkout" name="check_out_date" placeholder="Select date" readonly
                            class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800 cursor-pointer placeholder-slate-300">
                    </div>
                    <div class="w-full md:flex-1 px-8 py-4">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Guests</label>
                        <select id="loc-guests" name="guests" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800 appearance-none cursor-pointer">
                            @for ($i = 1; $i <= ($featuredRoomTypes->first()?->max_occupancy ?? 1); $i++)
                                <option value="{{ $i }}">{{ $i }} guest{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                        </select>
                    </div>
                    <div class="w-full md:w-auto p-1">
                        <button type="submit" class="w-full md:px-10 py-4 md:py-5 cursor-pointer rounded-2xl md:rounded-full bg-primary text-white hover:bg-primary-dark
                        transition-all uppercase tracking-widest text-sm font-bold shadow-lg transform hover:scale-[1.02]">
                            Quick Booking
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </section>

    <section id="rooms" class="py-32">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-20" data-aos="fade-right">
                <div class="max-w-xl">
                    <h2 class="font-serif text-4xl md:text-6xl mb-6 text-black font-bold italic">Curated Spaces</h2>
                    <p class="text-slate-500 text-lg leading-relaxed">Each room is a masterpiece of design, blending local Nepalese artistry with modern minimalist luxury.</p>
                </div>
                <a href="{{ route('web.rooms.index', $location->slug) }}" class="mt-8 md:mt-0 inline-flex items-center gap-3 text-black border-b-2 border-black pb-2 font-bold hover:text-primary hover:border-primary transition-all group">
                    View All Accommodations
                    <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach($featuredRoomTypes as $roomType)
                <a href="{{ route('web.rooms.show', [$location->slug, $roomType->id]) }}" class="block group bg-white rounded-[2.5rem] cursor-pointer overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    <div class="relative aspect-[4/5] overflow-hidden">
                        <img src="{{ $roomType->thumbnail ? asset('storage/' . $roomType->thumbnail) : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=800' }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $roomType->name }}">
                        <div class="absolute top-6 left-6 glass px-5 py-2 rounded-full text-sm font-bold text-slate-900 shadow-sm">
                            Rs.{{ $roomType->price_per_night }} <span class="text-[10px] font-normal text-slate-500 italic">/ night</span>
                        </div>
                    </div>
                    <div class="p-10">
                        <h3 class="font-serif text-3xl font-bold text-black mb-3 group-hover:text-primary transition-colors">{{ $roomType->name }}</h3>
                        <span class="inline-flex items-center gap-2 font-bold text-slate-900 group-hover:gap-4 transition-all">
                            Explore Room <span>→</span>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 mb-16">
            <div class="text-center" data-aos="fade-up">
                <span class="uppercase tracking-[0.1em] text-md text-primary font-bold mb-4 block">Guest Experiences</span>
                <h2 class="font-serif font-bold text-4xl md:text-5xl text-black italic">What They Say</h2>
                <div class="w-24 h-1 bg-black mx-auto mt-8"></div>
            </div>
        </div>

        @if($reviews->count() >= 4)
        <div class="marquee-container flex overflow-hidden w-full relative group">
            <!-- Fade gradients to blend sides into background -->
            <div class="absolute inset-y-0 left-0 w-16 md:w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-16 md:w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

            <!-- Track 1 -->
            <div class="marquee-track flex gap-6 md:gap-8 px-3 md:px-4">
                @foreach($reviews as $review)
                <div class="flex-none w-[90vw] md:w-[30rem] bg-slate-50 p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between transform transition-transform duration-300 hover:-translate-y-2">
                    <div>
                        <div class="flex items-center mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-3xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                                @endfor
                        </div>
                        @if($review->type === 'room_type' && $review->roomType)
                        <p class="text-xs text-[#A89070] font-bold uppercase tracking-widest mb-2">Stayed in {{ $review->roomType->name }}</p>
                        @endif
                        <p class="text-slate-600 text-base leading-relaxed mb-8 line-clamp-4">"{{ $review->body }}"</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200/50">
                        @if($review->avatar)
                        <img src="{{ asset('storage/' . $review->avatar) }}" alt="{{ $review->name }}"
                             class="w-14 h-14 rounded-full object-cover shadow-inner flex-shrink-0">
                        @else
                        <div class="w-14 h-14 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-xl shadow-inner flex-shrink-0">
                            {{ substr($review->name, 0, 1) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-bold text-base text-slate-900">{{ $review->name }}</p>
                            <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Verified Guest</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Track 2 (Duplicate for seamless loop) -->
            <div class="marquee-track flex gap-6 md:gap-8 px-3 md:px-4" aria-hidden="true">
                @foreach($reviews as $review)
                <div class="flex-none w-[90vw] md:w-[30rem] bg-slate-50 p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between transform transition-transform duration-300 hover:-translate-y-2">
                    <div>
                        <div class="flex items-center mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-3xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                                @endfor
                        </div>
                        @if($review->type === 'room_type' && $review->roomType)
                        <p class="text-xs text-[#A89070] font-bold uppercase tracking-widest mb-2">Stayed in {{ $review->roomType->name }}</p>
                        @endif
                        <p class="text-slate-600 text-base leading-relaxed mb-8 line-clamp-4">"{{ $review->body }}"</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200/50">
                        @if($review->avatar)
                        <img src="{{ asset('storage/' . $review->avatar) }}" alt="{{ $review->name }}"
                             class="w-14 h-14 rounded-full object-cover shadow-inner flex-shrink-0">
                        @else
                        <div class="w-14 h-14 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-xl shadow-inner flex-shrink-0">
                            {{ substr($review->name, 0, 1) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-bold text-base text-slate-900">{{ $review->name }}</p>
                            <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Verified Guest</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @elseif($reviews->count() > 0)
        <div class="flex flex-wrap justify-center gap-6 md:gap-8 px-6 sm:px-12 lg:px-24 pb-8">
            @foreach($reviews as $review)
            <div class="w-full md:w-[30rem] bg-slate-50 p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-3xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                        @endfor
                    </div>
                    @if($review->type === 'room_type' && $review->roomType)
                    <p class="text-xs text-[#A89070] font-bold uppercase tracking-widest mb-2">Stayed in {{ $review->roomType->name }}</p>
                    @endif
                    <p class="text-slate-600 text-base leading-relaxed mb-8 line-clamp-4">"{{ $review->body }}"</p>
                </div>
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200/50">
                    @if($review->avatar)
                    <img src="{{ asset('storage/' . $review->avatar) }}" alt="{{ $review->name }}"
                         class="w-14 h-14 rounded-full object-cover shadow-inner flex-shrink-0">
                    @else
                    <div class="w-14 h-14 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-xl shadow-inner flex-shrink-0">
                        {{ substr($review->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <p class="font-bold text-base text-slate-900">{{ $review->name }}</p>
                        <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Verified Guest</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-slate-500 italic pb-8">
            Check back soon for our latest guest experiences.
        </div>
        @endif
    </section>

    <section class="relative py-32 md:py-48 overflow-hidden bg-black/75">
        <div class="absolute inset-0 z-0">
            <img src="{{ $webInfo->homepage_end_image ? asset('storage/' . $webInfo->homepage_end_image) : 'https://cdn1.prayagsamagam.com/media/2025/05/20170044/3-42.webp' }}"
                class="w-full h-full object-cover opacity-30" alt="DwellCasa">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto text-center px-6" data-aos="fade-up">
            <h2 class="font-serif font-bold text-5xl md:text-7xl text-white mb-10 italic">
                {{ $webInfo->front_page_end_heading }}
            </h2>
            <p class="text-white/60 text-lg md:text-xl mb-14 max-w-xl mx-auto font-light leading-relaxed">
                {{ $webInfo->front_page_end_sub_heading }}
            </p>
            <a href="{{ route('contact', $location->slug) }}"
                class="inline-block bg-white text-slate-900 px-14 py-6 rounded-full font-bold text-xl hover:scale-105 transition-transform shadow-2xl hover:bg-blue-50">
                Make an Inquiry Before You Book
            </a>
        </div>
    </section>

</main>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection

@push('scripts')
<script>
    (function() {
        const BOOKED_BY_RT = @json($bookedDatesByRoomType ?? []);

        const checkinInput = document.getElementById('loc-checkin');
        const checkoutInput = document.getElementById('loc-checkout');
        const roomTypeSelect = document.getElementById('loc-roomtype');

        if (!checkinInput || !checkoutInput) return;

        function toLocalYMD(date) {
            return date.getFullYear() + '-' +
                String(date.getMonth() + 1).padStart(2, '0') + '-' +
                String(date.getDate()).padStart(2, '0');
        }

        function getBooked() {
            const id = roomTypeSelect ? roomTypeSelect.value : null;
            return (id && BOOKED_BY_RT[id]) ? BOOKED_BY_RT[id] : [];
        }

        let checkinPicker = null;
        let checkoutPicker = null;

        function initPickers() {
            const booked = getBooked();
            let checkInDate = null;
            let checkOutDate = null;

            if (checkinPicker) {
                checkinPicker.destroy();
                checkinPicker = null;
            }
            if (checkoutPicker) {
                checkoutPicker.destroy();
                checkoutPicker = null;
            }

            checkoutPicker = flatpickr(checkoutInput, {
                minDate: 'today',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                disable: [d => booked.includes(toLocalYMD(d))],
                disableMobile: true,
                onChange(selected) {
                    checkOutDate = selected[0] || null;
                }
            });

            checkinPicker = flatpickr(checkinInput, {
                minDate: 'today',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                disable: [d => booked.includes(toLocalYMD(d))],
                disableMobile: true,
                onChange(selected) {
                    checkInDate = selected[0] || null;

                    if (checkInDate && checkOutDate && checkOutDate <= checkInDate) {
                        checkOutDate = null;
                        checkoutPicker.clear();
                    }

                    if (checkoutPicker) {
                        checkoutPicker.set('minDate', checkInDate ?
                            new Date(checkInDate.getTime() + 86400000) :
                            'today');

                        // block checkout past the next booked date after checkin
                        let maxDate = null;
                        if (checkInDate) {
                            const ciStr = toLocalYMD(checkInDate);
                            for (const d of [...booked].sort()) {
                                if (d > ciStr) {
                                    maxDate = d;
                                    break;
                                }
                            }
                        }
                        checkoutPicker.set('maxDate', maxDate);

                        if (maxDate && checkOutDate && toLocalYMD(checkOutDate) > maxDate) {
                            checkOutDate = null;
                            checkoutPicker.clear();
                        }
                    }
                }
            });
        }

        initPickers();

        const guestsSelect = document.getElementById('loc-guests');

        function rebuildGuests() {
            if (!roomTypeSelect || !guestsSelect) return;
            const selected = roomTypeSelect.options[roomTypeSelect.selectedIndex];
            const max = parseInt(selected.dataset.max) || 1;
            const current = parseInt(guestsSelect.value) || 1;
            guestsSelect.innerHTML = '';
            for (let i = 1; i <= max; i++) {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = i === 1 ? '1 guest' : `${i} guests`;
                if (i === Math.min(current, max)) opt.selected = true;
                guestsSelect.appendChild(opt);
            }
        }

        if (roomTypeSelect) {
            roomTypeSelect.addEventListener('change', () => {
                initPickers();
                rebuildGuests();
            });
        }

        rebuildGuests();
    })();
</script>
@endpush