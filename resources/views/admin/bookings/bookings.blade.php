@extends('layouts.admin')

@section('title', 'Booking Management - DwellCasa Admin')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Booking Management</h1>
        <p class="text-slate-500 mt-1">View and manage all guest reservations and inquiries.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Add New Booking</h2>
            </div>
            <form id="add-booking-form" action="#" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guest Name <span class="text-red-500">*</span></label>
                        <input type="text" name="guest_name" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guest Email <span class="text-red-500">*</span></label>
                        <input type="email" name="guest_email" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guest Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="guest_phone" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Check In <span class="text-red-500">*</span></label>
                            <input type="date" name="check_in_date" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Check Out <span class="text-red-500">*</span></label>
                            <input type="date" name="check_out_date" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                        <select name="room_type_id" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            <option value="">Select a room...</option>
                            @foreach($roomTypes ?? [] as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Guests</label>
                            <input type="number" name="num_guests" min="1" value="1" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Stay Type</label>
                            <select name="stay_type" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                                <option value="short_term">Short Term</option>
                                <option value="long_term">Long Term</option>
                            </select>
                        </div>
                    </div>

                    <!-- Conditional Rate Fields -->
                    <div id="rate-per-night-wrapper">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Night <span class="text-red-500">*</span></label>
                        <input type="number" name="rate_per_night" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>

                    <div id="rate-per-month-wrapper" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Month <span class="text-red-500">*</span></label>
                        <input type="number" name="rate_per_month" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Total Amount <span class="text-red-500">*</span></label>
                            <input type="number" name="total_amount" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Deposit Amount</label>
                            <input type="number" name="deposit_amount" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" value="0">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Amount Paid</label>
                            <input type="number" name="amount_paid" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" value="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Payment Status</label>
                            <select name="payment_status" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                                <option value="unpaid">Unpaid</option>
                                <option value="deposit_paid">Deposit Paid</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="fully_paid">Fully Paid</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. extra pillows, late check-in..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Admin Notes (Internal)</label>
                        <textarea name="admin_notes" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Internal notes about this booking..."></textarea>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="w-full bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="xl:col-span-2">
        <!-- Filters / Tabs -->
        <div class="mb-6 border-b border-slate-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <a href="#" class="border-[#A89070] text-[#A89070] whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    All Bookings
                </a>
                <a href="#" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Upcoming
                </a>
                <a href="#" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    In-house
                </a>
                <a href="#" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
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
                            <th class="p-4 font-medium">Amount</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 mb-0.5">BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="font-medium text-slate-700">{{ $booking->guest_name }}</div>
                                <div class="text-slate-500 text-xs">{{ $booking->guest_email }}</div>
                            </td>
                            <td class="p-4 text-slate-700 font-medium">
                                {{ $booking->roomType->name ?? 'Room ID: ' . $booking->room_type_id }}
                            </td>
                            <td class="p-4 text-slate-700">
                                <div class="font-medium text-slate-900">{{ $booking->check_in_date ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">to {{ $booking->check_out_date ?? 'N/A' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-slate-900">Rs. {{ number_format($booking->total_amount ?? $booking->amount ?? 0, 0) }}</div>
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
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button class="text-slate-400 hover:text-slate-900 transition-colors font-medium">View</button>
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-[#A89070] hover:text-[#8E795E] transition-colors font-medium">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                No bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('add-booking-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/api/bookings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                alert('Error adding booking: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred.');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const stayTypeSelect = document.querySelector('select[name="stay_type"]');
        const ratePerNightWrapper = document.getElementById('rate-per-night-wrapper');
        const ratePerMonthWrapper = document.getElementById('rate-per-month-wrapper');
        
        if (stayTypeSelect && ratePerNightWrapper && ratePerMonthWrapper) {
            const ratePerNightInput = ratePerNightWrapper.querySelector('input');
            const ratePerMonthInput = ratePerMonthWrapper.querySelector('input');

            function toggleRateFields() {
                if (stayTypeSelect.value === 'short_term') {
                    ratePerNightWrapper.classList.remove('hidden');
                    ratePerNightInput.required = true;
                    
                    ratePerMonthWrapper.classList.add('hidden');
                    ratePerMonthInput.required = false;
                    ratePerMonthInput.value = '';
                } else { // long_term
                    ratePerNightWrapper.classList.add('hidden');
                    ratePerNightInput.required = false;
                    ratePerNightInput.value = '';

                    ratePerMonthWrapper.classList.remove('hidden');
                    ratePerMonthInput.required = true;
                }
            }

            toggleRateFields();
            stayTypeSelect.addEventListener('change', toggleRateFields);
        }
    });
</script>
@endpush
