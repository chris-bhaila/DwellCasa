<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'DwellCasa - Luxury Hotel Booking')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'DM Sans', sans-serif;
        }

        h1,
        h2,
        h3,
        .font-serif {
            font-family: 'Monsterrat', serif;
            line-height: 1.2 !important;
        }

        /* FIXED OVERLAP BUG */

        /* Smooth Nav Transition */
        .nav-transition {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Modern Glassmorphism */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Flash Message Animation */
        @keyframes slideIn {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('seo')
    @stack('head')
</head>

<body class="antialiased bg-[#fbfbf9] text-primary" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 50)" :class="mobileMenu ? 'overflow-hidden' : ''">

    <nav
        class="fixed w-full z-[100] nav-transition"
        :class="scrolled ? 'glass-nav py-3' : 'bg-transparent py-6'">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ $location ? route('location.home', $location->slug) : route('home') }}"
                        class="text-2xl font-bold tracking-tighter nav-transition"
                        :class="{{ request()->routeIs('home', 'location.home') ? '!scrolled' : 'false' }} ? 'text-white' : 'text-slate-900'">
                        DwellCasa<span class="text-primary">.</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-10">
                    @php
                    $navItems = [
                    'Rooms' => 'web.rooms.index',
                    'Gallery' => 'gallery',
                    'About' => 'about',
                    'Contact' => 'contact',
                    ];
                    @endphp

                    @foreach($navItems as $name => $route)
                    <a href="{{ $location ? route($route, $location->slug) : '#' }}"
                        class="text-md font-medium tracking-wide uppercase hover:opacity-100 transition-opacity nav-transition"
                        :class="{{ request()->routeIs('home', 'location.home') ? '!scrolled' : 'false' }} ? 'text-white/80 hover:text-white' : 'text-black font-bold hover:text-primary'">
                        {{ $name }}
                    </a>
                    @endforeach
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}"
                        class="px-6 py-2.5 rounded-full text-sm font-bold tracking-wider uppercase transition-all shadow-lg active:scale-95"
                        :class="scrolled ? 'bg-white text-primary hover:bg-[#A89070] hover:text-white':'bg-primary text-white hover:bg-white hover:text-primary'">
                        View Locations
                    </a>

                    <button class="md:hidden p-2" @click="mobileMenu = true">
                        <svg class="w-6 h-6" :class="{{ request()->routeIs('home', 'location.home') ? '!scrolled' : 'false' }} ? 'text-white' : 'text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div>
        <template x-teleport="body">
            <div x-show="mobileMenu"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-0 z-[200] bg-primary flex flex-col" x-cloak>

                <!-- Header -->
                <div class="flex items-center justify-between px-8 py-6 border-b border-white/20">
                    <a href="{{ route('home') }}" @click="mobileMenu = false" class="text-2xl font-bold tracking-tighter text-white">
                        DwellCasa<span class="text-white/60">.</span>
                    </a>
                    <button @click="mobileMenu = false" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Nav links -->
                <nav class="flex-1 flex flex-col justify-center px-8 space-y-2">
                    @foreach($navItems as $name => $route)
                    <a href="{{ $location ? route($route, $location->slug) : '#' }}"
                        @click="mobileMenu = false"
                        class="flex items-center justify-between group py-4 border-b border-white/10 last:border-0">
                        <span class="text-2xl font-bold tracking-wide text-white group-hover:text-white/70 transition-colors">{{ $name }}</span>
                        <svg class="w-5 h-5 text-white/40 group-hover:text-white/70 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @endforeach
                </nav>

                <!-- Footer -->
                <div class="px-8 py-6 border-t border-white/20">
                    <div class="flex items-center gap-6">
                        @if(isset($webInfo))
                        <a href="{{ $webInfo->facebook_link }}" class="text-sm font-medium text-white/60 hover:text-white transition-colors uppercase tracking-widest">Facebook</a>
                        <a href="{{ $webInfo->instagram_link }}" class="text-sm font-medium text-white/60 hover:text-white transition-colors uppercase tracking-widest">Instagram</a>
                        @endif
                    </div>
                </div>
            </div>
        </template>
    </div>

    <main>
        <div class="fixed top-24 right-6 z-[150] space-y-4 max-w-sm w-full">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="glass-nav bg-green-50/90 border-l-4 border-green-500 p-4 rounded-xl shadow-xl animate-slide-in">
                <div class="flex items-center">
                    <div class="flex-shrink-0 text-green-500">✨</div>
                    <div class="ml-3 font-medium text-green-800">{{ session('success') }}</div>
                </div>
            </div>
            @endif
        </div>

        <div class="{{ request()->routeIs('home', 'location.home') ? '' : 'pt-16' }}">
            @yield('content')
        </div>
    </main>

    <footer class="bg-[#71614b] text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
                <div class="col-span-1 md:col-span-1">
                    <h3 class="text-3xl font-bold mb-6 tracking-tighter">DwellCasa<span class="text-white">.</span></h3>
                    <p class="text-white leading-relaxed font-light">
                        {{ $webInfo->footer_description }}
                    </p>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-white mb-8">Navigation</h4>
                    <ul class="space-y-4 text-white">
                        <li><a href="{{ $location ? route('web.rooms.index', $location->slug) : '#' }}" class="hover:text-white transition-colors">Suites & Rooms</a></li>
                        <li><a href="{{ $location ? route('gallery', $location->slug) : '#' }}" class="hover:text-white transition-colors">Visual Gallery</a></li>
                        <li><a href="{{ $location ? route('about', $location->slug) : '#' }}" class="hover:text-white transition-colors">Our Heritage</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-white mb-8">Contact</h4>
                    <ul class="space-y-4 text-white font-light">
                        <li class="flex items-center gap-3">📍 {{ $webInfo->contact_address }}</li>
                        <li class="flex items-center gap-3">📞 {{ implode(', ', array_filter((array) ($webInfo?->contact_phone ?? []))) }}</li>
                        <li class="flex items-center gap-3">✉️ {{ $webInfo->contact_email }}</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-white mb-8">Connect</h4>
                    <div class="flex space-x-6">
                        <a href="{{ $webInfo->facebook_link }}" class="text-white hover:text-white transition-transform hover:-translate-y-1">Facebook</a>
                        <a href="{{ $webInfo->instagram_link }}" class="text-white hover:text-white transition-transform hover:-translate-y-1">Instagram</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row justify-between items-center gap-6 text-white text-xs uppercase tracking-widest">
                <p>&copy; {{ date('Y') }} DwellCasa Boutique Hotel. All rights reserved.</p>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white">Privacy Policy</a>
                    <a href="#" class="hover:text-white">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: true
            });
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>