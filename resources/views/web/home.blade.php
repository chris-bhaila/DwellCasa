@extends('layouts.app')

@section('title', 'DwellCasa - Luxury Stays in Lalitpur')

@section('content')

@push('head')
<style>
    /* Typography & Core Styling */
    .font-serif { font-family: 'Cormorant Garamond', serif; }
    .font-sans { font-family: 'DM Sans', sans-serif; }
    
    html { scroll-behavior: smooth; }
    body { font-family: 'DM Sans', sans-serif; background-color: #fbfbf9; overflow-x: hidden; }

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
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }

    /* Custom Scrollbar for Luxury Feel */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }

    /* Remove default date styling for cleaner UI */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }
</style>
@endpush

<main>

    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Pashupatinaath0587.JPG" 
                 class="hero-zoom w-full h-full object-cover opacity-50" alt="DwellCasa Interior">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-900/60"></div>
        </div>
        
        <div class="relative z-10 text-center text-white px-6 max-w-5xl" data-aos="fade-up" data-aos-duration="1200">
            <span class="font-serif uppercase tracking-[0.1em] text-xs md:text-xl mb-6 block font-medium opacity-90">Rooted in Culture, Refined in Comfort</span>
            <h1 class="font-serif text-5xl font-extrabold md:text-8xl mb-8 italic">Experience <span class="text-[#F0C97A]">Elegance.</span></h1>
            <p class="text-base md:text-xl font-light max-w-2xl mx-auto mb-12 leading-relaxed opacity-80">
                A sanctuary of sophisticated comfort nestled in the heart of Lalitpur. Your journey to tranquility starts here.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="#rooms" class="w-full sm:w-auto bg-white text-slate-900 px-10 py-4 rounded-full font-bold hover:bg-slate-100 transition-all transform hover:-translate-y-1 shadow-xl">
                    Explore Suites
                </a>
                <a href="#booking-form" class="w-full sm:w-auto border border-white/40 backdrop-blur-md px-10 py-4 rounded-full font-semibold hover:bg-white/20 transition-all">
                    Quick Booking
                </a>
            </div>
        </div>
    </section>

    <section id="booking-form" class="relative z-30 -mt-10 md:-mt-14 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="glass shadow-2xl rounded-3xl md:rounded-full p-2 md:p-3" data-aos="zoom-in" data-aos-delay="200">
                <form action="{{ route('booking.create') }}" method="GET" class="flex flex-col md:flex-row items-center gap-1">
                    <div class="w-full md:flex-1 px-8 py-4 border-b md:border-b-0 md:border-r border-slate-200/50">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Check In</label>
                        <input type="date" name="check_in_date" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800">
                    </div>
                    <div class="w-full md:flex-1 px-8 py-4 border-b md:border-b-0 md:border-r border-slate-200/50">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Check Out</label>
                        <input type="date" name="check_out_date" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800">
                    </div>
                    <div class="w-full md:flex-1 px-8 py-4">
                        <label class="block text-[10px] uppercase tracking-widest text-slate-400 mb-1 font-bold">Room Type</label>
                        <select name="room_type_id" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold w-full text-slate-800 appearance-none cursor-pointer">
                            @foreach($featuredRoomTypes as $roomType)
                                <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-auto p-1">
                        <button type="submit" class="w-full md:px-10 py-4 md:py-5 rounded-2xl md:rounded-full bg-primary text-white hover:bg-primary-dark
                        transition-all uppercase tracking-widest text-xs font-bold shadow-lg transform hover:scale-[1.02]">
                            Check Availability
                        </button>
                    </div>
                </form>
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
                <a href="{{ route('web.rooms.index') }}" class="mt-8 md:mt-0 inline-flex items-center gap-3 text-black border-b-2 border-black pb-2 font-bold hover:text-primary hover:border-primary transition-all group">
                    View All Accommodations
                    <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach($featuredRoomTypes as $roomType)
                <a href="{{ route('web.rooms.show', $roomType->id) }}" class="block group bg-white rounded-[2.5rem] cursor-pointer overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    <div class="relative aspect-[4/5] overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=800" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $roomType->name }}">
                        <div class="absolute top-6 left-6 glass px-5 py-2 rounded-full text-sm font-bold text-slate-900 shadow-sm">
                            Rs.{{ $roomType->price_per_night }} <span class="text-[10px] font-normal text-slate-500 italic">/ night</span>
                        </div>
                    </div>
                    <div class="p-10">
                        <h3 class="font-serif text-3xl font-bold text-black mb-3 group-hover:text-primary transition-colors">{{ $roomType->name }}</h3>
                        <div class="flex flex-wrap gap-4 text-[10px] text-slate-400 mb-8 uppercase tracking-[0.2em] font-bold">
                            <span>45 m²</span>
                            <span class="text-slate-200">•</span>
                            <span>City View</span>
                            <span class="text-slate-200">•</span>
                            <span>King Bed</span>
                        </div>
                        <span class="inline-flex items-center gap-2 font-bold text-slate-900 group-hover:gap-4 transition-all">
                            Explore Room <span>→</span>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-6" data-aos="fade-up">
                <span class="uppercase tracking-[0.1em] text-md text-primary font-bold mb-4 block">World Class Facilities</span>
                <h2 class="font-serif font-bold text-4xl md:text-5xl text-black italic">Unrivaled Excellence</h2>
                <div class="w-24 h-1 bg-black mx-auto mt-8"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-12">
                @foreach($amenities as $amenity)
                <div class="group text-center p-10 rounded-[3rem] hover:bg-slate-50 transition-all duration-300 cursor-pointer" data-aos="zoom-in">
                    <div class="w-16 h-16 text-black bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-8 group-hover:bg-slate-900 group-hover:text-white transition-all transform group-hover:rotate-[10deg] shadow-inner">
                        <span class="text-2xl flex items-center justify-center">
                            @if($amenity->icon)
                                @if(str_contains($amenity->icon, '<'))
                                    {!! $amenity->icon !!}
                                @else
                                    <i data-lucide="{{ $amenity->icon }}" class="w-8 h-8"></i>
                                @endif
                            @else
                                ✨
                            @endif
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-black mb-2">{{ $amenity->name }}</h3>
                    <!-- <p class="text-sm text-slate-400 font-light italic">{{ $amenity->description }}</p> -->
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative py-32 md:py-48 overflow-hidden bg-black/75">
        <div class="absolute inset-0 z-0">
            <img src="https://cdn1.prayagsamagam.com/media/2025/05/20170044/3-42.webp" 
                 class="w-full h-full object-cover opacity-30" alt="Footer Background">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto text-center px-6" data-aos="fade-up">
            <h2 class="font-serif font-bold text-5xl md:text-7xl text-white mb-10 italic">
                Ready for an <br> unforgettable escape?
            </h2>
            <p class="text-white/60 text-lg md:text-xl mb-14 max-w-xl mx-auto font-light leading-relaxed">
                Experience the height of luxury. Book directly with us for the best rate guarantee and exclusive spa vouchers.
            </p>
            <a href="{{ route('booking.create') }}" 
               class="inline-block bg-white text-slate-900 px-14 py-6 rounded-full font-bold text-xl hover:scale-105 transition-transform shadow-2xl hover:bg-blue-50">
                Reserve Your Suite Now
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