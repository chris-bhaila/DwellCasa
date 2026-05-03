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
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
            <label class="text-xs font-medium text-slate-500 whitespace-nowrap">From</label>
            <input type="date" name="from" value="{{ $from }}"
                class="text-sm font-medium text-slate-800 bg-transparent border-none outline-none focus:ring-0 cursor-pointer">
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
            <label class="text-xs font-medium text-slate-500 whitespace-nowrap">To</label>
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
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
            <i class="bi bi-receipt text-lg"></i>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Total Billed</p>
        <p class="text-2xl font-bold text-slate-900">Rs. {{ number_format($billed, 0) }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $bookings->count() }} booking{{ $bookings->count() !== 1 ? 's' : '' }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mb-3">
            <i class="bi bi-check-circle text-lg"></i>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Collected</p>
        <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($collected, 0) }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $collectPct }}% collection rate</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 mb-3">
            <i class="bi bi-hourglass-split text-lg"></i>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Outstanding</p>
        <p class="text-2xl font-bold {{ $outstanding > 0 ? 'text-rose-600' : 'text-slate-400' }}">Rs. {{ number_format($outstanding, 0) }}</p>
        <p class="text-xs text-slate-400 mt-1">Unpaid balance</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="w-10 h-10 rounded-xl bg-[#A89070]/10 flex items-center justify-center text-[#A89070] mb-3">
            <i class="bi bi-graph-up text-lg"></i>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Collection Rate</p>
        <p class="text-2xl font-bold text-slate-900">{{ $collectPct }}%</p>
        <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
            <div class="h-1.5 rounded-full transition-all {{ $collectPct >= 80 ? 'bg-green-500' : ($collectPct >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}"
                style="width: {{ $collectPct }}%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- By Room Type -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">By Room Type</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($byRoomType as $name => $data)
            <div class="px-5 py-4">
                <div class="flex justify-between items-start mb-1.5">
                    <span class="text-sm font-medium text-slate-800">{{ $name }}</span>
                    <span class="text-xs text-slate-400">{{ $data['count'] }} booking{{ $data['count'] !== 1 ? 's' : '' }}</span>
                </div>
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-slate-500">Billed: <span class="font-semibold text-slate-700">Rs. {{ number_format($data['billed'], 0) }}</span></span>
                    <span class="{{ $data['outstanding'] > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                        Outstanding: Rs. {{ number_format($data['outstanding'], 0) }}
                    </span>
                </div>
                @php $pct = $data['billed'] > 0 ? min(100, round(($data['collected'] / $data['billed']) * 100)) : 0; @endphp
                <div class="w-full bg-slate-100 rounded-full h-1">
                    <div class="h-1 rounded-full bg-[#A89070]" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-slate-400 italic text-center">No data for this period.</p>
            @endforelse
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Bookings</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Guest</th>
                        <th class="px-5 py-3 font-medium">Room Type</th>
                        <th class="px-5 py-3 font-medium">Check In</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Billed</th>
                        <th class="px-5 py-3 font-medium text-right">Paid</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-900">{{ $booking->guest->full_name ?? 'N/A' }}</p>
                            <p class="text-slate-400 text-xs">{{ $booking->guest->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $booking->roomType->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $booking->check_in_date->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusMap = [
                                    'confirmed'   => ['bg-green-50 text-green-700 border-green-200',  'Confirmed'],
                                    'pending'     => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Pending'],
                                    'checked_in'  => ['bg-blue-50 text-blue-700 border-blue-200',    'Checked In'],
                                    'checked_out' => ['bg-slate-50 text-slate-600 border-slate-200', 'Completed'],
                                ];
                                [$cls, $label] = $statusMap[$booking->status] ?? ['bg-slate-50 text-slate-500 border-slate-200', ucfirst($booking->status)];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-900">
                            Rs. {{ number_format($booking->total_amount, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($booking->amount_paid > 0)
                            <span class="font-semibold text-green-700">Rs. {{ number_format($booking->amount_paid, 0) }}</span>
                            @else
                            <span class="text-slate-400 text-xs italic">Not paid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400 text-sm italic">No bookings in this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
