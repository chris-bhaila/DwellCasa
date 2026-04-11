<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DwellCasa Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'DM Sans', sans-serif; background-color: #f8fafc; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .sidebar-link.active { background-color: #A89070; color: white; }
        .sidebar-link.active .sidebar-icon { color: white; }
        .sidebar-link:not(.active):hover { background-color: #1e293b; }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="flex h-screen antialiased bg-slate-50 overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
        <div class="flex items-center justify-center h-20 border-b border-slate-800 relative">
            <h1 class="text-2xl font-serif font-bold italic text-white tracking-wide">DwellCasa</h1>
            <button @click="sidebarOpen = false" class="absolute right-4 text-slate-400 hover:text-white lg:hidden">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.bookings') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Bookings</span>
            </a>
            <a href="{{ route('admin.room_type.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.room_type*') ? 'active' : '' }}">
                <i class="bi bi-door-open sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Rooms</span>
            </a>
            <a href="/admin/amenities" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/amenities*') ? 'active' : '' }}">
                <i class="bi bi-gem sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Amenities</span>
            </a>
            <a href="/admin/gallery" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/gallery*') ? 'active' : '' }}">
                <i class="bi bi-images sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Gallery</span>
            </a>
            <a href="/admin/inquiries" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/inquiries*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text sidebar-icon text-slate-400 mr-3 text-lg"></i>
                <span class="font-medium">Inquiries</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left flex items-center mt-4 px-4 py-2 rounded-lg text-slate-400 hover:bg-red-500/20 hover:text-red-300 transition-colors">
                    <i class="bi bi-box-arrow-left mr-3 text-lg"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden w-full">
        <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between lg:justify-end px-4 lg:px-8 shadow-sm z-10">
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-[#A89070] focus:outline-none lg:hidden">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center">
                <!-- Header content can go here, e.g. search bar, notifications -->
            </div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-8">
            @yield('content')
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('scripts')
</body>
</html>