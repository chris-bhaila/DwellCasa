@extends('layouts.admin')

@section('title', 'Revenue - DwellCasa Admin')
@section('header_title', 'Revenue')

@section('content')

<!-- Header & Date Filter -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Revenue</h1>
        <p class="text-slate-500 mt-1">Bookings with check-in dates between the selected range.</p>
    </div>

    <form method="GET" action="{{ route('admin.revenue') }}" class="flex items-center gap-2 flex-wrap">
        @if($filter)   <input type="hidden" name="filter"    value="{{ $filter }}">@endif
        @if($roomType) <input type="hidden" name="room_type" value="{{ $roomType }}">@endif
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
            <label class="text-sm font-medium text-slate-500 whitespace-nowrap">From</label>
            <input type="date" name="from" value="{{ $from }}"
                class="text-sm font-medium text-slate-800 bg-transparent border-none outline-none focus:ring-0 cursor-pointer">
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
            <label class="text-sm font-medium text-slate-500 whitespace-nowrap">To</label>
            <input type="date" name="to" value="{{ $to }}"
                class="text-sm font-medium text-slate-800 bg-transparent border-none outline-none focus:ring-0 cursor-pointer">
        </div>
        <button type="submit"
            class="bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm text-sm">
            Apply
        </button>
    </form>
</div>

<!-- Summary Cards -->
@php
    // Base params shared by all card links — room_type is always preserved
    $baseRt = array_filter(['from' => $from, 'to' => $to, 'room_type' => $roomType]);

    // Toggle helpers: clicking the active card clears it, otherwise sets it
    $billedUrl      = route('admin.revenue', $baseRt);  // always clears dashboard filter
    $collectedUrl   = route('admin.revenue', array_filter(array_merge($baseRt, ['filter' => $filter === 'collected'   ? null : 'collected'])));
    $outstandingUrl = route('admin.revenue', array_filter(array_merge($baseRt, ['filter' => $filter === 'outstanding' ? null : 'outstanding'])));
    $discountedUrl  = route('admin.revenue', array_filter(array_merge($baseRt, ['filter' => $filter === 'discounted'  ? null : 'discounted'])));

    $hasFilter = $filter || $roomType;
    $clearUrl  = route('admin.revenue', ['from' => $from, 'to' => $to]);
@endphp

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

    <!-- Total Billed — clears dashboard filter, shows all -->
    <a href="{{ $billedUrl }}"
        class="rounded-2xl border shadow-sm p-5 transition-all block
            {{ !$filter ? 'bg-slate-50 border-slate-300 ring-2 ring-slate-200' : 'bg-white border-slate-100 hover:border-slate-300 hover:shadow-md' }}">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
            <i class="bi bi-receipt text-lg"></i>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Total Billed</p>
        <p class="text-2xl font-bold text-slate-900">Rs. {{ number_format($billed, 0) }}</p>
        <p class="text-sm text-slate-400 mt-1">{{ $totalBookingCount }} booking{{ $totalBookingCount !== 1 ? 's' : '' }}</p>
    </a>

    <!-- Collected -->
    <a href="{{ $collectedUrl }}"
        class="rounded-2xl border shadow-sm p-5 transition-all block
            {{ $filter === 'collected' ? 'bg-green-50 border-green-300 ring-2 ring-green-200' : 'bg-white border-slate-100 hover:border-green-200 hover:shadow-md' }}">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mb-3">
            <i class="bi bi-check-circle text-lg"></i>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">
            Collected
            @if($filter === 'collected')<span class="ml-1 text-green-400 text-sm">&times; clear</span>@endif
        </p>
        <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($collected, 0) }}</p>
        <p class="text-sm text-slate-400 mt-1">{{ $collectPct }}% collection rate</p>
    </a>

    <!-- Outstanding -->
    <a href="{{ $outstandingUrl }}"
        class="rounded-2xl border shadow-sm p-5 transition-all block
            {{ $filter === 'outstanding' ? 'bg-rose-50 border-rose-300 ring-2 ring-rose-200' : 'bg-white border-slate-100 hover:border-rose-200 hover:shadow-md' }}">
        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 mb-3">
            <i class="bi bi-hourglass-split text-lg"></i>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">
            Outstanding
            @if($filter === 'outstanding')<span class="ml-1 text-rose-400 text-sm">&times; clear</span>@endif
        </p>
        <p class="text-2xl font-bold {{ $outstanding > 0 ? 'text-rose-600' : 'text-slate-400' }}">Rs. {{ number_format($outstanding, 0) }}</p>
        <p class="text-sm text-slate-400 mt-1">Unpaid balance</p>
    </a>

    <!-- Discounts -->
    <a href="{{ $discountedUrl }}"
        class="rounded-2xl border shadow-sm p-5 transition-all block
            {{ $filter === 'discounted' ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-200' : 'bg-white border-slate-100 hover:border-amber-200 hover:shadow-md' }}">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 mb-3">
            <i class="bi bi-tag text-lg"></i>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">
            Discounts
            @if($filter === 'discounted')<span class="ml-1 text-amber-400 text-sm">&times; clear</span>@endif
        </p>
        <p class="text-2xl font-bold {{ $totalDiscount > 0 ? 'text-amber-600' : 'text-slate-400' }}">Rs. {{ number_format($totalDiscount, 0) }}</p>
        <p class="text-sm text-slate-400 mt-1">Total given</p>
    </a>

    <!-- Collection Rate (static) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="w-10 h-10 rounded-xl bg-[#A89070]/10 flex items-center justify-center text-[#A89070] mb-3">
            <i class="bi bi-graph-up text-lg"></i>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-1">Collection Rate</p>
        <p class="text-2xl font-bold text-slate-900">{{ $collectPct }}%</p>
        <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
            <div class="h-1.5 rounded-full transition-all {{ $collectPct >= 80 ? 'bg-green-500' : ($collectPct >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}"
                style="width: {{ $collectPct }}%"></div>
        </div>
    </div>

</div>

<!-- Room Type Filter Bar -->
@if($byRoomType->isNotEmpty())
<div class="flex flex-wrap items-center gap-2 mb-6">

    @foreach($byRoomType->keys() as $rtName)
    @php
        $rtActive = $roomType === $rtName;
        // Preserve dashboard filter; toggle this room type
        $rtUrl = route('admin.revenue', array_filter([
            'from'      => $from,
            'to'        => $to,
            'filter'    => $filter,
            'room_type' => $rtActive ? null : $rtName,
        ]));
    @endphp
    @endforeach

    @if($hasFilter)
    <a href="{{ $clearUrl }}"
        class="ml-auto inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-sm font-medium
               bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 transition-all">
        <i class="bi bi-x-circle"></i> Clear all filters
    </a>
    @endif
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- By Room Type -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">By Room Type</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($byRoomType as $rtName => $data)
            @php
                $rtActive = $roomType === $rtName;
                $rtUrl = route('admin.revenue', array_filter([
                    'from'      => $from,
                    'to'        => $to,
                    'filter'    => $filter,
                    'room_type' => $rtActive ? null : $rtName,
                ]));
            @endphp
            <a href="{{ $rtUrl }}"
                class="block px-5 py-4 transition-colors {{ $rtActive ? 'bg-[#A89070]/5' : 'hover:bg-slate-50/60' }}">
                <div class="flex justify-between items-start mb-1.5">
                    <span class="text-sm font-medium {{ $rtActive ? 'text-[#A89070]' : 'text-slate-800' }}">
                        {{ $rtName }}
                        @if($rtActive)<i class="bi bi-funnel-fill text-sm ml-1"></i>@endif
                    </span>
                    <span class="text-sm text-slate-400">{{ $data['count'] }} booking{{ $data['count'] !== 1 ? 's' : '' }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-500">Billed: <span class="font-semibold text-slate-700">Rs. {{ number_format($data['billed'], 0) }}</span></span>
                    <span class="{{ $data['outstanding'] > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                        Outstanding: Rs. {{ number_format($data['outstanding'], 0) }}
                    </span>
                </div>
                @if($data['discount'] > 0)
                <div class="text-sm text-amber-600 mb-1.5">
                    Discount: Rs. {{ number_format($data['discount'], 0) }}
                </div>
                @endif
                @php $pct = $data['billed'] > 0 ? min(100, round(($data['collected'] / $data['billed']) * 100)) : 0; @endphp
                <div class="w-full bg-slate-100 rounded-full h-1 mt-2">
                    <div class="h-1 rounded-full bg-[#A89070]" style="width: {{ $pct }}%"></div>
                </div>
            </a>
            @empty
            <p class="px-5 py-8 text-sm text-slate-400 italic text-center">No data for this period.</p>
            @endforelse
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-2 flex-wrap">
            <div>
                <h2 class="text-lg font-serif font-bold text-slate-900 italic">Bookings</h2>
                @if($filter || $roomType)
                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                    @if($roomType)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-medium bg-[#A89070]/10 text-[#A89070]">
                        <i class="bi bi-building"></i> {{ $roomType }}
                    </span>
                    @endif
                    @if($filter === 'outstanding')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-medium bg-rose-50 text-rose-600">
                        <i class="bi bi-hourglass-split"></i> Outstanding
                    </span>
                    @elseif($filter === 'collected')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-medium bg-green-50 text-green-700">
                        <i class="bi bi-check-circle"></i> Fully Collected
                    </span>
                    @elseif($filter === 'discounted')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-medium bg-amber-50 text-amber-600">
                        <i class="bi bi-tag"></i> Discounted
                    </span>
                    @endif
                </div>
                @endif
            </div>
            <span class="text-sm text-slate-400 shrink-0">{{ $bookings->count() }} record{{ $bookings->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[620px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Guest</th>
                        <th class="px-5 py-3 font-medium">Room Type</th>
                        <th class="px-5 py-3 font-medium">Check In</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Billed</th>
                        <th class="px-5 py-3 font-medium text-right">Discount</th>
                        <th class="px-5 py-3 font-medium text-right">Paid</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-900">{{ $booking->guest->full_name ?? 'N/A' }}</p>
                            <p class="text-slate-400 text-sm">{{ $booking->guest->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $booking->roomType->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $booking->check_in_date->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusMap = [
                                    'confirmed'   => ['bg-green-50 text-green-700 border-green-200',   'Confirmed'],
                                    'pending'     => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Pending'],
                                    'checked_in'  => ['bg-blue-50 text-blue-700 border-blue-200',      'Checked In'],
                                    'checked_out' => ['bg-slate-50 text-slate-600 border-slate-200',   'Completed'],
                                ];
                                [$cls, $label] = $statusMap[$booking->status] ?? ['bg-slate-50 text-slate-500 border-slate-200', ucfirst($booking->status)];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium border {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-900">
                            Rs. {{ number_format($booking->total_amount, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if(($booking->discount ?? 0) > 0)
                            <span class="font-semibold text-amber-600">Rs. {{ number_format($booking->discount, 0) }}</span>
                            @else
                            <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($booking->amount_paid !== null)
                            <span class="font-semibold text-green-700">Rs. {{ number_format($booking->amount_paid, 0) }}</span>
                            <span class="text-slate-400 font-normal">/ {{ number_format($booking->total_amount, 0) }}</span>
                            @else
                            <span class="text-slate-400 text-sm italic">Not paid</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}"
                                class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-[#A89070] transition-colors rounded-md hover:bg-slate-100"
                                title="Edit booking">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-400 text-sm italic">
                            {{ ($filter || $roomType) ? 'No bookings match this filter.' : 'No bookings in this date range.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
