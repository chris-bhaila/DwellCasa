@extends('layouts.admin')

@section('title', 'Admin Dashboard - DwellCasa')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Dashboard Overview</h1>
        <p class="text-slate-500">Welcome back, Administrator. Here's what's happening today.</p>
    </div>
    <div class="flex gap-3">
        <!-- <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg font-medium hover:bg-slate-50 transition-all shadow-sm">
                Download Report
            </button> -->
        <a href="{{ route('admin.room_type.create') }}" class="bg-[#A89070] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#8E795E] transition-all shadow-md">
            + Add New Room
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-[#A89070]/10 rounded-xl flex items-center justify-center text-[#A89070]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-green-600 bg-green-50 px-2 py-1 rounded-md">+12%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Total Bookings</h3>
        <p class="text-2xl font-bold text-slate-900 mt-1">1,248</p>
    </div>

    <!-- Available Rooms -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <!-- <span class="text-sm font-medium text-red-600 bg-red-50 px-2 py-1 rounded-md">-2</span> -->
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Total Rooms</h3>
        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $rooms->count() }}</p>
    </div>

    <!-- Revenue -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-green-600 bg-green-50 px-2 py-1 rounded-md">+8.5%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Revenue (Month)</h3>
        <p class="text-2xl font-bold text-slate-900 mt-1">Rs. 854,000</p>
    </div>

    <!-- Active Guests -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-md">Steady</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Active Guests</h3>
        <p class="text-2xl font-bold text-slate-900 mt-1">48</p>
    </div>
</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Bookings List -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Recent Bookings</h2>
            <a href="#" class="text-[#A89070] text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="p-4 font-medium">Guest</th>
                        <th class="p-4 font-medium">Room Type</th>
                        <th class="p-4 font-medium">Check In/Out</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @foreach ($bookings as $booking)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4">
                            <p class="font-medium text-slate-900">{{ $booking->guest->full_name ?? 'N/A' }}</p>
                            <p class="text-slate-500 text-xs">{{ $booking->guest->email ?? 'N/A' }}</p>
                        </td>
                        <td class="p-4 text-slate-700">{{ $booking->roomtype->name }}</td>
                        <td class="p-4 text-slate-700">
                            <p>{{ $booking->check_in_date ? \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') : 'N/A' }}</p>
                            <p class="text-xs text-slate-400">{{ $booking->check_out_date ? \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') : 'N/A' }}</p>
                        </td>
                        <td class="p-4">
                            @if($booking->status === 'confirmed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                            @elseif($booking->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                            @elseif($booking->status === 'checked_in')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Checked In</span>
                            @elseif($booking->status === 'checked_out')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">Completed</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                            @endif
                        </td>
                        <td class="p-4 font-medium text-slate-900">Rs. {{ number_format($booking->amount_paid, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions & Room Status -->
    <div class="space-y-8">
        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.bookings') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors group text-slate-600">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm font-medium">New Booking</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors group text-slate-600">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="text-sm font-medium">Payments</span>
                </a>
                <a href="{{ route('admin.room_type.index') }}" class="flex flex-col items-center justify-center text-center p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors group text-slate-600">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-sm font-medium">Room Management</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-[#A89070] hover:text-white transition-colors group text-slate-600">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Settings</span>
                </a>
            </div>
        </div>

        <!-- Room Availability -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-4">Availability</h2>
            <div class="space-y-4">
                @foreach ($roomTypes as $roomType)
                @php
                $totalRooms = $roomType->rooms()->count();
                $availableRooms = $roomType->rooms()->where('status', 'available')->count();
                $percentage = $totalRooms > 0 ? round(($availableRooms / $totalRooms) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $roomType->name }}</span>
                        <span class="font-medium text-slate-900">{{ $availableRooms }}/{{ $totalRooms }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-[#A89070] h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection