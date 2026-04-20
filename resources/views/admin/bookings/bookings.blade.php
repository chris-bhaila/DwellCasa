@extends('layouts.admin')

@section('title', 'Booking Management - DwellCasa Admin')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Booking Management</h1>
        <p class="text-slate-500 mt-1">View and manage all guest reservations and inquiries.</p>
    </div>
    <div>
        <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center justify-center bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
            <i class="bi bi-plus-lg mr-2 text-lg"></i>
            Add Booking
        </a>
    </div>
</div>

<!-- List Section -->
<div>
    <!-- Filters / Tabs -->
    <div class="mb-6 border-b border-slate-200">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                class="{{ (!request('filter') || request('filter') === 'all') ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                All Bookings
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'upcoming']) }}"
                class="{{ request('filter') === 'upcoming' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Upcoming
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'inhouse']) }}"
                class="{{ request('filter') === 'inhouse' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                In-house
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'completed']) }}"
                class="{{ request('filter') === 'completed' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Completed
            </a>
        </nav>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="p-4 font-medium">Ref / Guest</th>
                        <th class="p-4 font-medium">Room Type</th>
                        <th class="p-4 font-medium">Check In / Out</th>
                        <th class="p-4 font-medium">Guests</th>
                        <th class="p-4 font-medium">Amount</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4">
                            <div class="font-bold text-slate-900 mb-0.5">{{ $booking->booking_ref }}</div>
                            <div class="font-medium text-slate-700">
                                {{ $booking->guest->full_name ?? 'N/A' }}
                            </div>
                            <div class="text-slate-500 text-xs">{{ $booking->guest->email ?? '' }}</div>
                        </td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ $booking->roomType->name ?? 'Room ID: ' . $booking->room_type_id }}
                        </td>
                        <td class="p-4 text-slate-700">
                            <div class="font-medium text-slate-900">
                                {{ $booking->check_in_date ? \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                to {{ $booking->check_out_date ? \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ $booking->num_guests ?? 'N/A' }}
                        </td>
                        <td class="p-4">
                            @if($booking->total_amount > 0)
                            <div class="font-medium text-slate-900">Rs. {{ number_format($booking->total_amount, 0) }}</div>
                            @else
                            <div class="font-medium text-slate-400 italic text-xs">Not set</div>
                            @endif
                            @if($booking->payment_status === 'paid')
                            <span class="text-xs text-green-600 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Paid</span>
                            @elseif($booking->payment_status === 'refunded')
                            <span class="text-xs text-slate-500 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>Refunded</span>
                            @else
                            <span class="text-xs text-red-500 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>Unpaid</span>
                            @endif
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
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors font-medium rounded-md hover:bg-slate-100">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-[#A89070] transition-colors font-medium rounded-md hover:bg-slate-100">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">
                            No bookings found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush