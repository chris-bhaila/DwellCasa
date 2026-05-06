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
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f8fafc;
        }

        .font-serif {
            font-family: 'Cormorant Garamond', serif;
        }

        .sidebar-link.active {
            background-color: #A89070;
            color: white;
        }

        .sidebar-link.active .sidebar-icon {
            color: white;
        }

        .sidebar-link:not(.active):hover {
            background-color: #1e293b;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>

<body class="flex h-screen antialiased bg-slate-50 overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"
         x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-72 lg:w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:shadow-none">

        <!-- Sidebar header -->
        <div class="flex items-center justify-between px-5 h-20 border-b border-slate-800 flex-shrink-0">
            <h1 class="text-3xl font-bold font-serif">DwellCasa<span class="text-primary">.</span></h1>
            <button @click="sidebarOpen = false"
                    class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Nav links -->
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            <a href="{{ route('admin') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            @can('view bookings')
            <a href="{{ route('admin.bookings') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Bookings</span>
            </a>
            @endcan

            @can('manage room types')
            <a href="{{ route('admin.room_type.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.room_type*') ? 'active' : '' }}">
                <i class="bi bi-door-open sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Rooms</span>
            </a>
            @endcan

            @can('manage guests')
            <a href="{{ route('admin.guests') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.guests*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Guests</span>
            </a>
            @endcan

            @can('view revenue')
            <a href="{{ route('admin.revenue') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.revenue*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Revenue</span>
            </a>
            @endcan

            @can('manage amenities')
            <a href="{{ route('admin.amenities') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/amenities*') ? 'active' : '' }}">
                <i class="bi bi-gem sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Amenities</span>
            </a>
            @endcan

            @can('manage gallery')
            <a href="{{ route('admin.gallery') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/gallery*') ? 'active' : '' }}">
                <i class="bi bi-images sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Gallery</span>
            </a>
            @endcan

            @can('manage inquiries')
            <a href="{{ route('admin.inquiry') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/inquiry*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Inquiries</span>
            </a>
            @endcan

            @can('view inventory')
            @php $inventoryActive = request()->routeIs('admin.inventory*'); @endphp
            <div x-data="{ open: {{ $inventoryActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-link w-full flex items-center px-4 py-3 rounded-lg transition-colors {{ $inventoryActive ? 'active' : '' }}">
                    <i class="bi bi-box-seam sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                    <span class="font-medium flex-1 text-left">Inventory</span>
                    <i class="bi text-xs transition-transform" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('admin.inventory') }}"
                        class="sidebar-link flex items-center px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.inventory') && !request()->routeIs('admin.inventory.supplies') && !request()->routeIs('admin.inventory.equipment') ? 'active' : '' }}">
                        <i class="bi bi-grid sidebar-icon text-slate-400 mr-3 text-base flex-shrink-0"></i>
                        <span>Overview</span>
                    </a>
                    <a href="{{ route('admin.inventory.supplies') }}"
                        class="sidebar-link flex items-center px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.inventory.supplies') ? 'active' : '' }}">
                        <i class="bi bi-droplet sidebar-icon text-slate-400 mr-3 text-base flex-shrink-0"></i>
                        <span>Supplies</span>
                    </a>
                    <a href="{{ route('admin.inventory.equipment') }}"
                        class="sidebar-link flex items-center px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.inventory.equipment') ? 'active' : '' }}">
                        <i class="bi bi-tv sidebar-icon text-slate-400 mr-3 text-base flex-shrink-0"></i>
                        <span>Equipment</span>
                    </a>
                </div>
            </div>
            @endcan

            @can('manage reviews')
            <a href="{{ route('admin.reviews') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <i class="bi bi-star-half sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Reviews</span>
            </a>
            @endcan

            <!-- @can('manage users')
            <a href="{{ route('admin.users') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people sidebar-icon text-slate-400 mr-3 text-lg flex-shrink-0"></i>
                <span class="font-medium">Users</span>
            </a>
            @endcan -->
        </nav>

        <!-- Profile & logout -->
        <div class="flex-shrink-0 border-t border-slate-800 p-4 space-y-2">
            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors group">
                <div class="w-10 h-10 rounded-full bg-[#A89070] flex items-center justify-center font-bold text-white text-sm flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-semibold text-white truncate group-hover:text-[#A89070] transition-colors">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-red-500/20 hover:text-red-300 transition-colors">
                    <i class="bi bi-box-arrow-left text-lg flex-shrink-0"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden w-full">
        <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-4 lg:px-8 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="text-slate-500 hover:text-[#A89070] focus:outline-none lg:hidden">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                @hasSection('header_title')
                <h2 class="hidden lg:block text-3xl font-serif font-bold italic tracking-normal text-slate-800">@yield('header_title')</h2>
                @endif
            </div>
            <div class="flex items-center">
                @if(auth()->user()->hasRole('super_admin'))
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-500">Location:</span>
                    <select onchange="switchLocation(this.value)" class="...">
                        <option value="" disabled {{ !session('selected_location_id') ? 'selected' : '' }}>
                            Select Location
                        </option>
                        @foreach(\App\Models\Location::where('is_active', true)->get() as $location)
                        <option value="{{ $location->id }}"
                            {{ session('selected_location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Toast container -->
    <div id="admin-toast-container" class="fixed top-6 right-6 z-[200] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <!-- Confirm modal -->
    <div id="admin-confirm-modal" class="fixed inset-0 z-[300] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-md p-6">
            <div class="flex items-start gap-4 mb-6">
                <div id="admin-confirm-icon" class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-0.5"></div>
                <p id="admin-confirm-message" class="text-slate-700 text-sm leading-relaxed pt-2 whitespace-pre-line"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button id="admin-confirm-cancel" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancel</button>
                <button id="admin-confirm-ok" class="px-4 py-2 rounded-xl text-sm font-medium text-white transition-colors"></button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['Accept'] = 'application/json';
    </script>
    <script>
    window.adminConfirm = function(message, options) {
        options = options || {};
        var confirmLabel = options.confirmLabel || 'Confirm';
        var type = options.type || 'danger';

        return new Promise(function(resolve) {
            var modal   = document.getElementById('admin-confirm-modal');
            var msgEl   = document.getElementById('admin-confirm-message');
            var okBtn   = document.getElementById('admin-confirm-ok');
            var cancelBtn = document.getElementById('admin-confirm-cancel');
            var iconEl  = document.getElementById('admin-confirm-icon');

            var styles = {
                danger:  { btn: 'bg-red-600 hover:bg-red-700',         iconCls: 'bi-exclamation-triangle-fill text-red-500',  iconBg: 'bg-red-100' },
                warning: { btn: 'bg-amber-500 hover:bg-amber-600',      iconCls: 'bi-exclamation-circle-fill text-amber-500',  iconBg: 'bg-amber-100' },
                primary: { btn: 'bg-[#A89070] hover:bg-[#8E795E]',      iconCls: 'bi-question-circle-fill text-[#A89070]',     iconBg: 'bg-[#A89070]/10' },
            };
            var s = styles[type] || styles.danger;

            msgEl.textContent = message;
            okBtn.textContent = confirmLabel;
            okBtn.className   = 'px-4 py-2 rounded-xl text-sm font-medium text-white transition-colors ' + s.btn;
            iconEl.className  = 'w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-0.5 ' + s.iconBg;
            iconEl.innerHTML  = '<i class="bi ' + s.iconCls + ' text-lg"></i>';

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            function cleanup() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                okBtn.removeEventListener('click', onOk);
                cancelBtn.removeEventListener('click', onCancel);
                modal.removeEventListener('click', onBackdrop);
            }
            function onOk()      { cleanup(); resolve(true); }
            function onCancel()  { cleanup(); resolve(false); }
            function onBackdrop(e) { if (e.target === modal) { cleanup(); resolve(false); } }

            okBtn.addEventListener('click', onOk);
            cancelBtn.addEventListener('click', onCancel);
            modal.addEventListener('click', onBackdrop);
        });
    };

    window.adminToast = function(message, type) {
        type = type || 'error';
        var container = document.getElementById('admin-toast-container');
        var colors = {
            success: 'bg-green-50 border-green-400 text-green-800',
            error:   'bg-red-50 border-red-400 text-red-800',
            warning: 'bg-amber-50 border-amber-400 text-amber-800',
        };
        var icons = {
            success: 'bi-check-circle-fill text-green-500',
            error:   'bi-exclamation-circle-fill text-red-500',
            warning: 'bi-exclamation-triangle-fill text-amber-500',
        };
        var cls  = colors[type]  || colors.error;
        var icon = icons[type]   || icons.error;

        var toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-start gap-3 border-l-4 rounded-xl px-4 py-3 shadow-lg text-sm transition-all duration-300 opacity-0 translate-x-4 ' + cls;
        toast.innerHTML = '<i class="bi ' + icon + ' text-base shrink-0 mt-0.5"></i><span>' + message + '</span>';
        container.appendChild(toast);

        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                toast.classList.remove('opacity-0', 'translate-x-4');
            });
        });

        setTimeout(function() {
            toast.classList.add('opacity-0', 'translate-x-4');
            setTimeout(function() { toast.remove(); }, 300);
        }, 4500);
    };
    </script>
    @if(auth()->user()->hasRole('super_admin'))
    <script>
        function switchLocation(locationId) {
            fetch('/admin/switch-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    location_id: locationId
                })
            }).then(() => window.location.reload());
        }
    </script>
    @endif
    @stack('scripts')
</body>

</html>