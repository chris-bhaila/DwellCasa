<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DwellCasa - Luxury Stays in Nepal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        body { font-family: 'DM Sans', sans-serif; background-color: #fbfbf9; overflow-x: hidden; }

        .nav-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .location-card-img { transition: transform 0.7s ease; }
        .location-card:hover .location-card-img { transform: scale(1.06); }
        .location-card-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.15) 60%, transparent 100%);
        }

        .marquee-track { flex-shrink: 0; display: flex; min-width: 100%; animation: scrollLeft 40s linear infinite; }
        .marquee-container:hover .marquee-track { animation-play-state: paused; }
        @keyframes scrollLeft { from { transform: translateX(0); } to { transform: translateX(-100%); } }
    </style>
</head>

<body class="antialiased" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    {{-- Nav --}}
    <nav class="fixed w-full z-[100] nav-transition" :class="scrolled ? 'glass-nav py-3' : 'bg-transparent py-6'">
        <div class="max-w-7xl mx-auto px-6 md:px-12 flex justify-around items-center">
            <a href="{{ route('home') }}"
               class="text-2xl font-bold tracking-tighter nav-transition"
               :class="scrolled ? 'text-slate-900' : 'text-slate-900'">
                DwellCasa<span class="text-[#A89070]">.</span>
            </a>
        </div>
    </nav>

    <main class="pt-24">

        {{-- Location Cards --}}
        <section id="locations" class="pt-10 pb-28 px-6">
            <div class="max-w-6xl mx-auto">

                <div class="text-center mb-20" data-aos="fade-up">
                    <span class="uppercase tracking-[0.12em] text-md font-bold text-[#A89070] mb-4 block">{{ $webInfo?->front_page_sub_heading_1 ?? 'Our Properties' }}</span>
                    <h2 class="font-serif text-4xl md:text-6xl font-bold italic text-slate-900">{{ $webInfo?->front_page_main_heading ?? 'Where Would You Like to Stay?' }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    @foreach($locations as $loc)
                    <a href="{{ route('location.home', $loc->slug) }}"
                       class="location-card group relative block rounded-[2.5rem] overflow-hidden aspect-[3/4] md:aspect-[4/5] shadow-lg hover:shadow-2xl transition-shadow duration-500"
                       data-aos="fade-up"
                       data-aos-delay="{{ $loop->index * 150 }}">

                        <img
                            src="{{ $loc->hero_image ? asset("storage/{$loc->hero_image}") : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=800' }}"
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

        {{-- Brand Promise --}}
        <section class="py-24 bg-white">
            <div class="max-w-4xl mx-auto px-6 text-center" data-aos="fade-up">
                <h2 class="font-serif text-4xl md:text-5xl font-bold italic text-slate-900 mb-8">
                    {{ $webInfo?->front_page_end_heading ?? 'The DwellCasa Standard' }}
                </h2>
                <p class="text-slate-500 text-lg leading-relaxed max-w-2xl mx-auto">
                    {{ $webInfo?->front_page_sub_heading_2 ?? 'Whether in the cultural heart of Patan or the vibrant energy of Thamel, every DwellCasa property delivers the same promise — thoughtful design, genuine hospitality, and an unmistakably Nepali sense of place.' }}
                </p>
            </div>
        </section>


    </main>

    <footer class="bg-[#71614b] text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 mb-20">
                <div>
                    <h3 class="text-3xl font-bold mb-6 tracking-tighter">DwellCasa<span class="text-white">.</span></h3>
                    <p class="text-white leading-relaxed font-light">
                        {{ $webInfo->footer_description }}
                    </p>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-white mb-8">Our Properties</h4>
                    <ul class="space-y-4 text-white">
                        @foreach($locations as $loc)
                        <li>
                            <a href="{{ route('location.home', $loc->slug) }}" class="hover:text-white/80 transition-colors">
                                {{ $loc->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-white mb-8">Contact</h4>
                    <ul class="space-y-4 text-white font-light">
                        <li class="flex items-center gap-3">📞 {{ implode(', ', array_filter((array) ($webInfo->contact_phone ?? []))) }}</li>
                        <li class="flex items-center gap-3">✉️ {{ $webInfo->contact_email }}</li>
                    </ul>
                    <div class="flex space-x-6 mt-8">
                        <a href="{{ $webInfo->facebook_link }}" class="text-white hover:text-white/80 transition-colors">Facebook</a>
                        <a href="{{ $webInfo->instagram_link }}" class="text-white hover:text-white/80 transition-colors">Instagram</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-12 flex flex-col md:flex-row justify-between items-center gap-6 text-white text-xs uppercase tracking-widest">
                <p>&copy; {{ date('Y') }} DwellCasa Boutique Hotel. All rights reserved.</p>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white/80">Privacy Policy</a>
                    <a href="#" class="hover:text-white/80">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({ duration: 1000, once: true });
        });
    </script>

</body>

</html>
