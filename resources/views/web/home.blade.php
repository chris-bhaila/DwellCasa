@extends('layouts.app')

@section('title', 'DwellCasa - Luxury Stays in Nepal')

@push('head')
<style>
    .font-serif { font-family: 'Cormorant Garamond', serif; }
    body { font-family: 'DM Sans', sans-serif; background-color: #fbfbf9; overflow-x: hidden; }

    .hero-zoom { animation: slowZoom 20s infinite alternate ease-in-out; }
    @keyframes slowZoom {
        from { transform: scale(1); }
        to   { transform: scale(1.1); }
    }

    .location-card-img { transition: transform 0.7s ease; }
    .location-card:hover .location-card-img { transform: scale(1.06); }

    .location-card-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.15) 60%, transparent 100%);
    }
</style>
@endpush

@section('content')
<main>

    {{-- ── Hero ──────────────────────────────────────────────────────── --}}
    <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0">
            <img
                src="{{ $webInfo->homepage_main_image ? asset('storage/' . $webInfo->homepage_main_image) : 'https://upload.wikimedia.org/wikipedia/commons/6/68/Pashupatinaath0587.JPG' }}"
                class="hero-zoom w-full h-full object-cover opacity-45"
                alt="DwellCasa Nepal"
            >
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-slate-900/70"></div>
        </div>

        <div class="relative z-10 text-center text-white px-6 max-w-4xl" data-aos="fade-up" data-aos-duration="1200">
            <span class="font-serif uppercase tracking-[0.12em] text-xs md:text-sm mb-6 block font-medium opacity-80">
                Two Locations. One Standard of Excellence.
            </span>
            <h1 class="font-serif text-5xl md:text-8xl font-extrabold mb-8 italic leading-tight">
                {{ $webInfo->front_page_main_heading ?? 'DwellCasa' }}
            </h1>
            <p class="text-base md:text-xl font-light max-w-2xl mx-auto mb-12 leading-relaxed opacity-75">
                {{ $webInfo->front_page_sub_heading_2 ?? 'Curated luxury stays in the heart of Nepal.' }}
            </p>
            <a href="#locations"
               class="inline-block border border-white/40 backdrop-blur-md px-10 py-4 rounded-full font-semibold hover:bg-white/20 transition-all">
                Choose Your Location ↓
            </a>
        </div>
    </section>

    {{-- ── Location Cards ────────────────────────────────────────────── --}}
    <section id="locations" class="py-28 px-6">
        <div class="max-w-6xl mx-auto">

            <div class="text-center mb-20" data-aos="fade-up">
                <span class="uppercase tracking-[0.12em] text-xs font-bold text-primary mb-4 block">Our Properties</span>
                <h2 class="font-serif text-4xl md:text-6xl font-bold italic text-slate-900">Where Would You Like to Stay?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                @foreach($locations as $loc)
                <a href="{{ route('location.home', $loc->slug) }}"
                   class="location-card group relative block rounded-[2.5rem] overflow-hidden aspect-[3/4] md:aspect-[4/5] shadow-lg hover:shadow-2xl transition-shadow duration-500"
                   data-aos="fade-up"
                   data-aos-delay="{{ $loop->index * 150 }}">

                    <img
                        src="{{ $loc->hero_image ? asset('storage/' . $loc->hero_image) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=800' }}"
                        class="location-card-img absolute inset-0 w-full h-full object-cover"
                        alt="{{ $loc->name }}"
                    >

                    <div class="location-card-overlay absolute inset-0"></div>

                    <div class="absolute inset-0 flex flex-col justify-end p-10 text-white">
                        <p class="text-xs uppercase tracking-[0.15em] font-bold opacity-70 mb-3">Nepal</p>
                        <h3 class="font-serif text-4xl md:text-5xl font-bold italic mb-4 leading-tight">
                            {{ $loc->name }}
                        </h3>
                        @if(isset($loc->short_description))
                        <p class="text-white/70 text-sm leading-relaxed mb-6 max-w-sm">
                            {{ $loc->short_description }}
                        </p>
                        @endif
                        <span class="inline-flex items-center gap-3 text-sm font-bold border-b border-white/40 pb-1 group-hover:border-white transition-colors">
                            Explore Property
                            <span class="group-hover:translate-x-2 transition-transform inline-block">→</span>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Brand Promise ─────────────────────────────────────────────── --}}
    <section class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-6 text-center" data-aos="fade-up">
            <h2 class="font-serif text-4xl md:text-5xl font-bold italic text-slate-900 mb-8">
                The DwellCasa Standard
            </h2>
            <p class="text-slate-500 text-lg leading-relaxed max-w-2xl mx-auto">
                Whether in the cultural heart of Patan or the vibrant energy of Thamel,
                every DwellCasa property delivers the same promise — thoughtful design,
                genuine hospitality, and an unmistakably Nepali sense of place.
            </p>
        </div>
    </section>

    {{-- ── Reviews ───────────────────────────────────────────────────── --}}
    @if(isset($reviews) && $reviews->count() > 0)
    <section class="py-24 bg-slate-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 mb-16">
            <div class="text-center" data-aos="fade-up">
                <span class="uppercase tracking-[0.1em] text-xs text-primary font-bold mb-4 block">Guest Experiences</span>
                <h2 class="font-serif font-bold text-4xl md:text-5xl text-black italic">What They Say</h2>
                <div class="w-24 h-1 bg-black mx-auto mt-8"></div>
            </div>
        </div>

        <style>
            .marquee-track { flex-shrink: 0; display: flex; min-width: 100%; animation: scrollLeft 40s linear infinite; }
            .marquee-container:hover .marquee-track { animation-play-state: paused; }
            @keyframes scrollLeft { from { transform: translateX(0); } to { transform: translateX(-100%); } }
        </style>

        <div class="marquee-container flex overflow-hidden w-full relative">
            <div class="absolute inset-y-0 left-0 w-16 md:w-32 bg-gradient-to-r from-slate-50 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-16 md:w-32 bg-gradient-to-l from-slate-50 to-transparent z-10 pointer-events-none"></div>

            @foreach([1, 2] as $track)
            <div class="marquee-track flex gap-6 md:gap-8 px-3 md:px-4" @if($track === 2) aria-hidden="true" @endif>
                @foreach($reviews as $review)
                <div class="flex-none w-[90vw] md:w-[30rem] bg-white p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center mb-4">
                            @for($i = 1; $i <= 5; $i++)
                            <span class="text-2xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                            @endfor
                        </div>
                        <p class="text-slate-600 text-base leading-relaxed mb-8 line-clamp-4">"{{ $review->body }}"</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200/50">
                        <div class="w-12 h-12 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-lg">
                            {{ substr($review->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-900">{{ $review->name }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-0.5">Verified Guest</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </section>
    @endif

</main>
@endsection