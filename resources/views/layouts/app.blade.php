<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DwellCasa') }} - @yield('title', 'Luxury Hotel Booking')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        [x-cloak] { display: none !important; }
        
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; line-height: 1.2 !important; } /* FIXED OVERLAP BUG */

        /* Smooth Nav Transition */
        .nav-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Modern Glassmorphism */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Flash Message Animation */
        @keyframes slideIn {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-in { animation: slideIn 0.5s ease-out forwards; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('seo')
    @stack('head')
</head>
<body class="antialiased bg-[#fbfbf9] text-slate-900" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <nav 
        class="fixed w-full z-[100] nav-transition"
        :class="scrolled ? 'glass-nav py-3' : 'bg-transparent py-6'"
    >
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" 
                       class="text-2xl font-bold tracking-tighter nav-transition"
                       :class="!scrolled && $root.querySelector('#hero-marker') ? 'text-white' : 'text-slate-900'">
                        DwellCasa<span class="text-blue-600">.</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-10">
                    @php $navItems = ['Home' => 'home', 'Rooms' => 'rooms', 'Gallery' => 'gallery', 'About' => 'about', 'Contact' => 'contact']; @endphp
                    
                    @foreach($navItems as $name => $route)
                        <a href="{{ route($route) }}" 
                           class="text-sm font-medium tracking-wide uppercase hover:opacity-100 transition-opacity nav-transition"
                           :class="!scrolled && $root.querySelector('#hero-marker') ? 'text-white/80 hover:text-white' : 'text-slate-600 hover:text-slate-900'">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('booking.create') }}" 
                       class="px-6 py-2.5 rounded-full text-sm font-bold tracking-wider uppercase transition-all shadow-lg active:scale-95"
                       :class="scrolled ? 'bg-slate-900 text-white hover:bg-blue-900' : 'bg-white text-slate-900 hover:bg-slate-100'">
                        Book Now
                    </a>
                    
                    <button class="md:hidden p-2" @click="mobileMenu = true">
                        <svg class="w-6 h-6" :class="!scrolled ? 'text-white' : 'text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div x-data="{ mobileMenu: false }" @open-menu.window="mobileMenu = true">
        <template x-teleport="body">
            <div x-show="mobileMenu" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[200] bg-slate-900 flex flex-col justify-center items-center text-center p-6" x-cloak>
                
                <button @click="mobileMenu = false" class="absolute top-8 right-8 text-white/50 hover:text-white">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="space-y-8">
                    @foreach($navItems as $name => $route)
                        <a href="{{ route($route) }}" class="block text-4xl font-serif text-white italic hover:text-blue-400 transition-colors">{{ $name }}</a>
                    @endforeach
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

        <div class="{{ request()->routeIs('home') ? '' : 'pt-32' }}">
            @yield('content')
        </div>
    </main>

    <footer class="bg-slate-950 text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
                <div class="col-span-1 md:col-span-1">
                    <h3 class="text-3xl font-bold mb-6 tracking-tighter">DwellCasa<span class="text-blue-600">.</span></h3>
                    <p class="text-slate-400 leading-relaxed font-light">
                        Redefining the art of hospitality in Lalitpur. Your sanctuary of sophisticated comfort and timeless elegance.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-blue-500 mb-8">Navigation</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="{{ route('rooms') }}" class="hover:text-white transition-colors">Suites & Rooms</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white transition-colors">Visual Gallery</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Our Heritage</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-blue-500 mb-8">Contact</h4>
                    <ul class="space-y-4 text-slate-400 font-light">
                        <li class="flex items-center gap-3">📍 Lalitpur, Nepal</li>
                        <li class="flex items-center gap-3">📞 +977 123 456 789</li>
                        <li class="flex items-center gap-3">✉️ info@dwellcasa.com</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs uppercase tracking-[0.3em] font-bold text-blue-500 mb-8">Connect</h4>
                    <div class="flex space-x-6">
                        <a href="#" class="text-slate-400 hover:text-white transition-transform hover:-translate-y-1">Facebook</a>
                        <a href="#" class="text-slate-400 hover:text-white transition-transform hover:-translate-y-1">Instagram</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row justify-between items-center gap-6 text-slate-500 text-xs uppercase tracking-widest">
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
            AOS.init({ duration: 1000, once: true });
        });
    </script>
    @stack('scripts')
</body>
</html>