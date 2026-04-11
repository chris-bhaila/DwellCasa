@extends('layouts.admin')

@section('title', 'Edit Booking - DwellCasa Admin')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Edit Booking</h1>
            <p class="text-slate-500 mt-1">Update details for booking #{{ $booking->booking_ref }}.</p>
        </div>
    </div>
</div>

<form id="edit-booking-form" action="#" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{ $booking->id }}">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Form Section -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Booking Details</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Guest Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Name <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_name" value="{{ old('guest_name', $booking->guest->full_name ?? $booking->guest_name) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Email <span class="text-red-500">*</span></label>
                                <input type="email" name="guest_email" value="{{ old('guest_email', $booking->guest->email ?? $booking->guest_email) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Phone Number</label>
                                <input type="text" name="guest_phone" value="{{ old('guest_phone', $booking->guest->phone ?? $booking->guest_phone) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Stay Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Stay Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Check In <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" value="{{ old('check_in_date', \Carbon\Carbon::parse($booking->check_in_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Check Out <span class="text-red-500">*</span></label>
                                <input type="date" name="check_out_date" value="{{ old('check_out_date', \Carbon\Carbon::parse($booking->check_out_date)->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                                <select name="room_type_id" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                                    <option value="">Select a room...</option>
                                    @foreach($roomTypes ?? [] as $room)
                                        <option value="{{ $room->id }}" {{ old('room_type_id', $booking->room_type_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guests</label>
                                <input type="number" name="num_guests" min="1" value="{{ old('num_guests', $booking->num_guests) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Special Requests</label>
                                <textarea name="special_requests" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. extra pillows, late check-in...">{{ old('special_requests', $booking->special_requests) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Financials</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Stay Type</label>
                                <select name="stay_type" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                                    <option value="short_term" {{ old('stay_type', $booking->stay_type) == 'short_term' ? 'selected' : '' }}>Short Term</option>
                                    <option value="long_term" {{ old('stay_type', $booking->stay_type) == 'long_term' ? 'selected' : '' }}>Long Term</option>
                                </select>
                            </div>
                            <div id="rate-per-night-wrapper">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Night <span class="text-red-500">*</span></label>
                                <input type="number" name="rate_per_night" value="{{ old('rate_per_night', $booking->rate_per_night) }}" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div id="rate-per-month-wrapper" class="hidden">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Month <span class="text-red-500">*</span></label>
                                <input type="number" name="rate_per_month" value="{{ old('rate_per_month', $booking->rate_per_month) }}" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Total Amount <span class="text-red-500">*</span></label>
                                <input type="number" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Deposit Amount</label>
                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $booking->deposit_amount) }}" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Amount Paid</label>
                                <input type="number" name="amount_paid" value="{{ old('amount_paid', $booking->amount_paid) }}" step="0.01" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-4 p-6">
                    <a href="{{ route('admin.bookings') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 px-6 py-3">Cancel</a>
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Status</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Booking Status</label>
                        <select name="status" form="edit-booking-form" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="checked_in" {{ old('status', $booking->status) == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="checked_out" {{ old('status', $booking->status) == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Status</label>
                        <select name="payment_status" form="edit-booking-form" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            <option value="unpaid" {{ old('payment_status', $booking->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="deposit_paid" {{ old('payment_status', $booking->payment_status) == 'deposit_paid' ? 'selected' : '' }}>Deposit Paid</option>
                            <option value="partially_paid" {{ old('payment_status', $booking->payment_status) == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="fully_paid" {{ old('payment_status', $booking->payment_status) == 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="refunded" {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="pt-6 border-t border-slate-100">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Admin Notes (Internal)</label>
                        <textarea name="admin_notes" form="edit-booking-form" rows="4" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Internal notes about this booking...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('edit-booking-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const bookingId = data.id;
        
        try {
            const response = await fetch(`/api/bookings/${bookingId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.bookings') }}";
            } else {
                const error = await response.json();
                let errorMessage = 'Error updating booking: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating the booking.');
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
                } else { // long_term
                    ratePerNightWrapper.classList.add('hidden');
                    ratePerNightInput.required = false;

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
