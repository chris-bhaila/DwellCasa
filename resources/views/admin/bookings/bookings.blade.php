@extends('layouts.admin')

@section('title', 'Booking Management - DwellCasa Admin')
@section('header_title', 'Bookings')

@section('content')
@if(session('info'))
<div class="mb-6 flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700">
    <i class="bi bi-info-circle flex-shrink-0"></i>
    {{ session('info') }}
</div>
@endif
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Booking Management</h1>
        <p class="text-slate-500 mt-1">View and manage all guest reservations and inquiries.</p>
    </div>
    @if($filter !== 'trashed')
    <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center justify-center bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm self-start md:self-auto">
        <i class="bi bi-plus-lg mr-2 text-lg"></i>
        Add Booking
    </a>
    @endif
</div>

<!-- List Section -->
<div>
    <!-- Filters / Tabs -->
    <div class="mb-6 border-b border-slate-200 overflow-x-auto">
        <nav class="flex min-w-max space-x-6" aria-label="Tabs">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                class="{{ (!request('filter') || request('filter') === 'all') ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                All Bookings
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'upcoming']) }}"
                class="{{ request('filter') === 'upcoming' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                Upcoming
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'inhouse']) }}"
                class="{{ request('filter') === 'inhouse' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                In-house
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'completed']) }}"
                class="{{ request('filter') === 'completed' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                Completed
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'trashed']) }}"
                class="{{ request('filter') === 'trashed' ? 'border-red-400 text-red-500' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-1.5">
                <i class="bi bi-trash3"></i> Trash
            </a>
        </nav>
    </div>

    @if($filter === 'trashed')
    <div class="mb-4 flex items-center gap-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
        <i class="bi bi-info-circle"></i>
        Deleted bookings are kept for 90 days before being permanently removed.
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                        <th class="p-4 font-medium">Ref / Guest</th>
                        <th class="p-4 font-medium">Room Type</th>
                        <th class="p-4 font-medium">Check In / Out</th>
                        <th class="p-4 font-medium w-16">Guests</th>
                        <th class="p-4 font-medium">Amount</th>
                        @if($filter === 'trashed')
                        <th class="p-4 font-medium">Deleted</th>
                        @else
                        <th class="p-4 font-medium">Status</th>
                        @endif
                        <th class="p-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $filter === 'trashed' ? 'opacity-75' : '' }}">
                        <td class="p-4">
                            <div class="font-bold text-slate-900 mb-0.5">{{ $booking->booking_ref }}</div>
                            <div class="font-medium text-slate-700">
                                {{ $booking->guest->full_name ?? 'N/A' }}
                            </div>
                            <div class="text-slate-500 text-sm">{{ $booking->guest->email ?? '' }}</div>
                        </td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ $booking->roomType->name ?? "Room ID: {$booking->room_type_id}" }}
                        </td>
                        <td class="p-4 text-slate-700">
                            @php
                                $useActualTimes = $filter === 'inhouse' || $filter === 'completed'
                                    || in_array($booking->status, ['checked_in', 'checked_out']);
                            @endphp
                            @if($useActualTimes)
                                <div class="font-medium text-slate-900">
                                    {{ $booking->checkIn?->checked_in_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                </div>
                                @if($filter !== 'inhouse')
                                <div class="text-sm text-slate-500 mt-0.5">
                                    @if($filter === 'completed' || $booking->status === 'checked_out')
                                        to {{ $booking->checkOut?->checked_out_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                    @else
                                        to {{ $booking->check_out_date ? \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') : 'N/A' }}
                                    @endif
                                </div>
                                @endif
                            @else
                                <div class="font-medium text-slate-900">
                                    {{ $booking->check_in_date ? \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') : 'N/A' }}
                                </div>
                                <div class="text-sm text-slate-500 mt-0.5">
                                    to {{ $booking->check_out_date ? \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') : 'N/A' }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ $booking->num_guests ?? 'N/A' }}
                        </td>
                        <td class="p-4">
                            @if($booking->total_amount > 0)
                                @php $totalReceived = ($booking->amount_paid ?? 0) + ($booking->deposit_amount ?? 0); @endphp
                                @if($booking->amount_paid !== null || $booking->deposit_amount !== null)
                                <div class="font-medium text-slate-900">
                                    Rs. {{ number_format($totalReceived, 0) }}
                                    <span class="text-slate-400 font-normal">/ {{ number_format($booking->total_amount, 0) }}</span>
                                </div>
                                @else
                                <div class="font-medium text-slate-900">Rs. {{ number_format($booking->total_amount, 0) }}</div>
                                @endif
                            @else
                            <div class="font-medium text-slate-400 italic text-sm">Not set</div>
                            @endif
                            @if($booking->payment_status === 'fully_paid')
                            <span class="text-sm text-green-600 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Fully Paid{{ ($booking->discount ?? 0) > 0 ? ' + Discount' : '' }}</span>
                            @elseif($booking->payment_status === 'deposit_paid')
                            <span class="text-sm text-blue-600 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>Deposit Paid</span>
                            @elseif($booking->payment_status === 'partially_paid')
                            <span class="text-sm text-orange-600 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span>Partially Paid</span>
                            @elseif($booking->payment_status === 'refunded')
                            <span class="text-sm text-slate-500 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>Refunded</span>
                            @else
                            <span class="text-sm text-red-600 font-medium flex items-center mt-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>Unpaid</span>
                            @endif
                        </td>
                        @if($filter === 'trashed')
                        <td class="p-4 text-slate-500 text-sm">
                            {{ $booking->deleted_at ? $booking->deleted_at->format('M d, Y') : '—' }}
                        </td>
                        @else
                        <td class="p-4">
                            @if($booking->status === 'confirmed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                            @elseif($booking->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                            @elseif($booking->status === 'checked_in')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Checked In</span>
                            @elseif($booking->status === 'checked_out')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200">Completed</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                            @endif
                        </td>
                        @endif
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($filter === 'trashed')
                                <button
                                    onclick="restoreBooking({{ $booking->id }}, '{{ $booking->booking_ref }}')"
                                    class="w-8 h-8 flex items-center justify-center text-green-500 hover:text-green-700 transition-colors rounded-md hover:bg-green-50"
                                    title="Restore">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button
                                    onclick="forceDeleteBooking({{ $booking->id }}, '{{ $booking->booking_ref }}')"
                                    class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 transition-colors rounded-md hover:bg-red-50"
                                    title="Delete permanently">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @else
                                @php $isEditable = $booking->isEditableBy(auth()->user()); @endphp
                                <a href="{{ route('admin.bookings.view', $booking->id) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors font-medium rounded-md hover:bg-slate-100">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($isEditable)
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-[#A89070] transition-colors font-medium rounded-md hover:bg-slate-100">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">
                            {{ $filter === 'trashed' ? 'No deleted bookings.' : 'No bookings found.' }}
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
@if($filter === 'trashed')
<script>
async function restoreBooking(id, ref) {
    if (!await adminConfirm(`Restore booking ${ref}?`, { confirmLabel: 'Restore', type: 'primary' })) return;

    fetch(`/api/bookings/${id}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.reload();
        else adminToast(data.message ?? 'Restore failed.');
    })
    .catch(() => adminToast('Restore failed.'));
}

async function forceDeleteBooking(id, ref) {
    if (!await adminConfirm(`Permanently delete booking ${ref}? This cannot be undone.`, { confirmLabel: 'Delete Permanently', type: 'danger' })) return;

    fetch(`/api/bookings/${id}/force`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.reload();
        else adminToast(data.message ?? 'Delete failed.');
    })
    .catch(() => adminToast('Delete failed.'));
}
</script>
@endif
@endpush
