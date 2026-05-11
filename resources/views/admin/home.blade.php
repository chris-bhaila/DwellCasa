@extends('layouts.admin')

@section('title', 'Admin Dashboard - DwellCasa')
@section('header_title', 'Dashboard')

@section('content')

{{-- Incomplete website info notifications (super_admin only) --}}
@if(auth()->user()->hasRole('super_admin') && isset($incompleteLocations) && $incompleteLocations->isNotEmpty())
<div class="mb-8 space-y-3">
    @foreach($incompleteLocations as $loc)
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-amber-50 border border-amber-200 px-5 py-4 rounded-xl shadow-sm">
        <div class="flex items-start sm:items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-amber-800">
                <span class="font-bold">{{ $loc->name }}</span> is missing website information and won't appear on the public website until it's filled in.
            </p>
        </div>
        <button type="button" onclick="switchAndGoToInfo({{ $loc->id }})"
            class="flex-shrink-0 text-sm font-bold bg-amber-200 hover:bg-amber-300 text-amber-900 px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
            Fill Website Info &rarr;
        </button>
    </div>
    @endforeach
</div>
@endif

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Dashboard</h1>
        <p class="text-slate-500 mt-1">{{ now()->format('l, F j, Y') }} &mdash; Welcome back, {{ Auth::user()->name }}.</p>
    </div>
</div>

<!-- Primary KPI Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

    @can('view bookings')
    <!-- Today's Arrivals -->
    <a href="{{ route('admin.bookings') }}?filter=upcoming" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:border-slate-200 transition-all block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#A89070]/10 flex items-center justify-center text-[#A89070]">
                <i class="bi bi-box-arrow-in-right text-lg"></i>
            </div>
            <span class="text-sm font-medium px-2 py-1 rounded-md bg-slate-100 text-slate-500">Today</span>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Today's Arrivals</p>
        <p class="text-3xl font-bold text-slate-900">{{ $todayArrivals }}</p>
        <p class="text-sm text-slate-400 mt-1">Confirmed &amp; pending check-ins</p>
    </a>
    @endcan

    @can('check-in guests')
    <!-- Guests In-House -->
    <a href="{{ route('admin.bookings') }}?filter=inhouse" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:border-slate-200 transition-all block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                <i class="bi bi-people text-lg"></i>
            </div>
            <span class="text-sm font-medium px-2 py-1 rounded-md bg-blue-50 text-blue-600">Live</span>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Guests In-House</p>
        <p class="text-3xl font-bold text-slate-900">{{ $inHouseCount }}</p>
        <p class="text-sm text-slate-400 mt-1">Currently checked in</p>
    </a>
    @endcan

    @can('view revenue')
    <!-- Monthly Revenue -->
    <a href="{{ route('admin.revenue') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:border-slate-200 transition-all block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                <i class="bi bi-currency-rupee text-lg"></i>
            </div>
            <span class="text-sm font-medium px-2 py-1 rounded-md bg-green-50 text-green-600">{{ now()->format('M') }}</span>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Revenue Collected</p>
        <p class="text-3xl font-bold text-slate-900">Rs. {{ number_format($monthlyRevenue, 0) }}</p>
        <p class="text-sm text-slate-400 mt-1">From check-ins this month</p>
    </a>
    @endcan

    @can('manage inquiries')
    <!-- Unreplied Inquiries -->
    <a href="{{ route('admin.inquiry') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:border-slate-200 transition-all block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                <i class="bi bi-chat-left-dots text-lg"></i>
            </div>
            @if($unrepliedInquiries > 0)
            <span class="text-sm font-bold px-2 py-1 rounded-md bg-rose-50 text-rose-600">Action needed</span>
            @else
            <span class="text-sm font-medium px-2 py-1 rounded-md bg-green-50 text-green-600">All clear</span>
            @endif
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Unreplied Inquiries</p>
        <p class="text-3xl font-bold text-slate-900">{{ $unrepliedInquiries }}</p>
        <p class="text-sm text-slate-400 mt-1">Waiting for a response</p>
    </a>
    @endcan

</div>

<!-- Secondary Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    @can('check-out guests')
    <a href="{{ route('admin.bookings') }}?filter=inhouse" class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-4 hover:shadow-md hover:border-slate-200 transition-all">
        <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center text-orange-400 flex-shrink-0">
            <i class="bi bi-box-arrow-right"></i>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Today's Departures</p>
            <p class="text-xl font-bold text-slate-900">{{ $todayDepartures }}</p>
        </div>
    </a>
    @endcan

    @can('manage rooms')
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-4">
        <div class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center text-teal-500 flex-shrink-0">
            <i class="bi bi-door-open"></i>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Rooms Available</p>
            <p class="text-xl font-bold text-slate-900">{{ $availableRooms }} <span class="text-sm font-normal text-slate-400">/ {{ $totalRooms }}</span></p>
        </div>
    </div>
    @endcan

    @can('manage reviews')
    <a href="{{ route('admin.reviews') }}" class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-4 hover:shadow-md hover:border-slate-200 transition-all">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 flex-shrink-0">
            <i class="bi bi-star-half"></i>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Avg. Review Score</p>
            <p class="text-xl font-bold text-slate-900">
                {{ $avgRating ? number_format($avgRating, 1) : '—' }}
                <span class="text-sm font-normal text-slate-400">/ 5</span>
            </p>
        </div>
    </a>
    @endcan

    @can('view bookings')
    <a href="{{ route('admin.bookings') }}" class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-4 hover:shadow-md hover:border-slate-200 transition-all">
        <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center text-violet-500 flex-shrink-0">
            <i class="bi bi-calendar-check"></i>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Bookings This Month</p>
            <p class="text-xl font-bold text-slate-900">{{ $monthlyBookings }}</p>
        </div>
    </a>
    @endcan

</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    @can('view bookings')
    <!-- Recent Bookings -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Recent Bookings</h2>
            <a href="{{ route('admin.bookings') }}" class="text-[#A89070] text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Guest</th>
                        <th class="px-5 py-3 font-medium">Room Type</th>
                        <th class="px-5 py-3 font-medium">Check In / Out</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.bookings.view', $booking->id) }}'">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-900 text-sm">{{ $booking->guest->full_name ?? 'N/A' }}</p>
                            <p class="text-slate-400 text-sm">{{ $booking->guest->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 text-sm">{{ $booking->roomType->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-slate-800 text-sm font-medium">{{ $booking->check_in_date ? $booking->check_in_date->format('M d, Y') : 'N/A' }}</p>
                            <p class="text-slate-400 text-sm">to {{ $booking->check_out_date ? $booking->check_out_date->format('M d, Y') : 'N/A' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($booking->status === 'confirmed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                            @elseif($booking->status === 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                            @elseif($booking->status === 'checked_in')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Checked In</span>
                            @elseif($booking->status === 'checked_out')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-slate-50 text-slate-600 border border-slate-200">Completed</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($booking->amount_paid !== null)
                            <p class="font-semibold text-slate-900 text-sm">
                                Rs. {{ number_format($booking->amount_paid, 0) }}
                                <span class="text-slate-400 font-normal">/ {{ number_format($booking->total_amount, 0) }}</span>
                            </p>
                            @elseif($booking->total_amount > 0)
                            <p class="font-medium text-slate-900 text-sm">Rs. {{ number_format($booking->total_amount, 0) }}</p>
                            @else
                            <p class="text-slate-400 text-sm italic">Not set</p>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm italic">No bookings yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcan

    <!-- Right Column -->
    <div class="space-y-6">

        <!-- Quick Actions -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-3">
                @can('create bookings')
                <a href="{{ route('admin.bookings.create') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors text-slate-600 text-center">
                    <i class="bi bi-plus-circle text-xl"></i>
                    <span class="text-sm font-medium leading-tight">New Booking</span>
                </a>
                @endcan
                @can('view bookings')
                <a href="{{ route('admin.bookings') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors text-slate-600 text-center">
                    <i class="bi bi-calendar3 text-xl"></i>
                    <span class="text-sm font-medium leading-tight">All Bookings</span>
                </a>
                @endcan
                @can('manage room types')
                <a href="{{ route('admin.room_type.index') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors text-slate-600 text-center">
                    <i class="bi bi-building text-xl"></i>
                    <span class="text-sm font-medium leading-tight">Rooms</span>
                </a>
                @endcan
                <a href="{{ route('admin.settings') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors text-slate-600 text-center">
                    <i class="bi bi-gear text-xl"></i>
                    <span class="text-sm font-medium leading-tight">Settings</span>
                </a>
            </div>
        </div>

        @can('manage room types')
        <!-- Room Availability -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic mb-4">Availability</h2>
            @php
            $barPalette = ['#A89070', '#60a5fa', '#34d399', '#f472b6', '#a78bfa', '#fb923c'];
            @endphp
            @forelse($roomTypes as $roomType)
            @php
                $total     = $roomType->rooms_count;
                $booked    = $roomType->active_bookings_count;
                $available = max(0, $total - $booked);
                $pct       = $total > 0 ? round(($available / $total) * 100) : 0;
                $color     = $barPalette[$loop->index % count($barPalette)];
            @endphp
            <div class="mb-3 last:mb-0">
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="flex items-center gap-1.5 text-slate-600 font-medium truncate pr-2">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $color }}"></span>
                        {{ $roomType->name }}
                    </span>
                    <span class="text-slate-900 font-semibold flex-shrink-0">{{ $available }}<span class="text-slate-400 font-normal">/{{ $total }}</span></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all" style="width: {{ $pct }}%; background-color: {{ $color }}"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 italic">No room types configured.</p>
            @endforelse
        </div>
        @endcan

    </div>
</div>

@can('view inventory')
<!-- Inventory Snapshot -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-serif font-bold text-slate-900 italic">Inventory Alerts</h2>
        <a href="{{ route('admin.inventory') }}"
           class="text-[#A89070] text-sm font-medium hover:underline">
            View Inventory
        </a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Low Stock -->
        <a href="{{ route('admin.inventory.supplies') }}"
           class="flex items-center gap-4 p-4 rounded-xl border transition-colors
               {{ $inventoryLowStock > 0
                   ? 'bg-amber-50 border-amber-200 hover:bg-amber-100'
                   : 'bg-slate-50 border-slate-100 hover:bg-slate-100' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $inventoryLowStock > 0 ? 'bg-amber-100' : 'bg-slate-100' }}">
                <i class="bi bi-exclamation-triangle
                    {{ $inventoryLowStock > 0 ? 'text-amber-500' : 'text-slate-400' }} text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold
                    {{ $inventoryLowStock > 0 ? 'text-amber-700' : 'text-slate-900' }}">
                    {{ $inventoryLowStock }}
                </p>
                <p class="text-sm text-slate-500 font-medium">Low Stock Items</p>
            </div>
        </a>

        <!-- Out of Stock -->
        <a href="{{ route('admin.inventory.supplies') }}"
           class="flex items-center gap-4 p-4 rounded-xl border transition-colors
               {{ $inventoryOutOfStock > 0
                   ? 'bg-rose-50 border-rose-200 hover:bg-rose-100'
                   : 'bg-slate-50 border-slate-100 hover:bg-slate-100' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $inventoryOutOfStock > 0 ? 'bg-rose-100' : 'bg-slate-100' }}">
                <i class="bi bi-x-circle
                    {{ $inventoryOutOfStock > 0 ? 'text-rose-500' : 'text-slate-400' }} text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold
                    {{ $inventoryOutOfStock > 0 ? 'text-rose-700' : 'text-slate-900' }}">
                    {{ $inventoryOutOfStock }}
                </p>
                <p class="text-sm text-slate-500 font-medium">Out of Stock</p>
            </div>
        </a>

        <!-- Damaged Equipment -->
        <a href="{{ route('admin.inventory.equipment') }}"
           class="flex items-center gap-4 p-4 rounded-xl border transition-colors
               {{ $inventoryDamaged > 0
                   ? 'bg-orange-50 border-orange-200 hover:bg-orange-100'
                   : 'bg-slate-50 border-slate-100 hover:bg-slate-100' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $inventoryDamaged > 0 ? 'bg-orange-100' : 'bg-slate-100' }}">
                <i class="bi bi-tools
                    {{ $inventoryDamaged > 0 ? 'text-orange-500' : 'text-slate-400' }} text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold
                    {{ $inventoryDamaged > 0 ? 'text-orange-700' : 'text-slate-900' }}">
                    {{ $inventoryDamaged }}
                </p>
                <p class="text-sm text-slate-500 font-medium">Damaged / Under Repair</p>
            </div>
        </a>

    </div>
</div>
@endcan

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    @can('manage inquiries')
    <!-- Recent Inquiries -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Unreplied Inquiries</h2>
            <a href="{{ route('admin.inquiry') }}" class="text-[#A89070] text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentInquiries as $inquiry)
            <div class="px-5 py-4 flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0 mt-0.5">
                    <i class="bi bi-person text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                        <span class="font-semibold text-slate-900 text-sm">{{ $inquiry->name }}</span>
                        <span class="text-sm px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 capitalize">{{ str_replace('_', ' ', $inquiry->inquiry_type) }}</span>
                    </div>
                    <p class="text-sm text-slate-500 truncate">{{ $inquiry->message }}</p>
                </div>
                <span class="text-sm text-slate-400 flex-shrink-0 mt-0.5">{{ $inquiry->created_at->diffForHumans(null, true) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm italic">
                <i class="bi bi-check2-circle text-2xl block mb-2 text-green-400"></i>
                No unreplied inquiries.
            </div>
            @endforelse
        </div>
    </div>
    @endcan

    @can('view revenue')
    <!-- Revenue Snapshot -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Revenue Snapshot</h2>
            <a href="{{ route('admin.revenue') }}" class="text-[#A89070] text-sm font-medium hover:underline">View Details</a>
        </div>

        @php
            $billed    = (float) $monthRevenueBilled;
            $collected = (float) $monthRevenueCollected;
            $outstanding = max(0, $billed - $collected);
            $collectPct  = $billed > 0 ? min(100, round(($collected / $billed) * 100)) : 0;
        @endphp

        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500">Total Billed</span>
                    <span class="font-bold text-slate-900">Rs. {{ number_format($billed, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500">Collected</span>
                    <span class="font-bold text-green-700">Rs. {{ number_format($collected, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Outstanding</span>
                    <span class="font-bold {{ $outstanding > 0 ? 'text-rose-600' : 'text-slate-400' }}">Rs. {{ number_format($outstanding, 0) }}</span>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm text-slate-500 mb-1.5">
                    <span>Collection rate</span>
                    <span class="font-semibold text-slate-700">{{ $collectPct }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full transition-all {{ $collectPct >= 80 ? 'bg-green-500' : ($collectPct >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}"
                        style="width: {{ $collectPct }}%"></div>
                </div>
            </div>

            @if($billed == 0)
            <p class="text-sm text-slate-400 italic text-center pt-2">No bookings with check-ins this month yet.</p>
            @endif
        </div>
    </div>
    @endcan

</div>

@push('scripts')
<script>
function switchAndGoToInfo(locationId) {
    fetch('{{ route('admin.switch-location') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ location_id: locationId }),
    }).then(() => {
        window.location.href = '{{ route('admin.info') }}';
    });
}
</script>
@endpush

@endsection
