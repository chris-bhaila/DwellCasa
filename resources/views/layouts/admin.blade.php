<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - DwellCasa')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #FBFBF9;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-serif {
            font-family: 'Monsterrat', serif;
        }

        /* Critical Layout Fallbacks (In case Tailwind JIT hasn't compiled yet) */
        aside.fixed {
            position: fixed !important;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .w-64 {
            width: 16rem !important;
        }

        .-translate-x-full {
            transform: translateX(-100%) !important;
        }

        .translate-x-0 {
            transform: translateX(0) !important;
        }

        @media (min-width: 1024px) {
            aside.lg\:translate-x-0 {
                transform: translateX(0) !important;
            }

            .lg\:pl-64 {
                padding-left: 16rem !important;
            }

            .lg\:hidden {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="antialiased text-slate-900 overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white flex flex-col transform -translate-x-full transition-transform duration-300 lg:translate-x-0"
        :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <a href="{{ url('/admin') }}" class="text-xl font-bold text-white">
                DwellCasa<span class="text-primary">.</span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Dashboard</span>
            </a>

            <!-- Bookings -->
            <a href="{{ route('admin.bookings') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin/bookings') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin/bookings'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin/bookings') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Bookings</span>
            </a>

            <!-- Rooms -->
            <a href="{{ route('admin.room_type.index') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin/rooms') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin/rooms'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin/rooms') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Rooms</span>
            </a>

            <!-- Amenities -->
            <a href="{{ url('admin/amenities') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin/amenities') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin/amenities'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin/amenities') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Amenities</span>
            </a>

            <!-- Gallery -->
            <a href="{{ url('admin/gallery') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin/gallery') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin/gallery'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin/gallery') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Gallery</span>
            </a>

            <!-- Inquiries -->
            <a href="{{ url('admin/inquiries') }}" class="relative flex items-center px-4 py-3 {{ request()->is('admin/inquiries') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-2xl group transition-all">
                @if(request()->is('admin/inquiries'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-[#A89070] rounded-r-full"></span>
                @endif
                <svg class="w-5 h-5 mr-3 {{ request()->is('admin/inquiries') ? 'text-[#A89070]' : 'text-slate-500 group-hover:text-[#A89070]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Inquiries</span>
            </a>

            <!-- Guests -->
            <a href="#" class="relative flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-2xl group transition-all">
                <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-[#A89070] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium text-sm tracking-wide">Guests</span>
            </a>

            <!-- Settings -->
            <div class="pt-6 mt-6 border-t border-white/5">
                <a href="#" class="relative flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-2xl group transition-all">
                    <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-[#A89070] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium text-sm tracking-wide">Settings</span>
                </a>
            </div>
        </nav>

        <div class="p-4">
            <form method="POST" action="#">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-2xl transition-all group">
                    <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium text-sm tracking-wide">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <!-- Search -->
                <div class="hidden sm:flex items-center bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 focus-within:ring-2 focus-within:ring-[#A89070] focus-within:border-transparent transition-all">
                    <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search..." class="bg-transparent border-none focus:ring-0 text-sm text-slate-700 w-64 p-0 placeholder-slate-400">
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="relative p-2 text-slate-500 hover:text-[#A89070] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center space-x-3 focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-[#A89070] text-white flex items-center justify-center font-bold text-sm">
                            AD
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-slate-700 leading-tight">Admin User</p>
                            <p class="text-xs text-slate-500">Manager</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen" x-transition.opacity class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50" x-cloak>
                        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Your Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="https://dwellcasa.com.np" target="_blank" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View Public Site</a>
                        <form method="POST" action="#">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>