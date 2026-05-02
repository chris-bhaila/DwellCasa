@extends('layouts.admin')

@section('title', 'Add New Booking - DwellCasa Admin')
@section('header_title', 'Add Booking')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Add New Booking</h1>
        <p class="text-slate-500 mt-1">Manually create a new reservation for a guest.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h2 class="text-xl font-serif font-bold text-slate-900 italic">Booking Details</h2>
    </div>
    <form id="add-booking-form" action="#" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Name <span class="text-red-500">*</span></label>
                <input type="text" name="guest_name" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Email <span class="text-red-500">*</span></label>
                <input type="email" name="guest_email" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Phone Number <span class="text-red-500">*</span></label>
                <input type="text" name="guest_phone" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
            </div>

            <div class="grid grid-cols-2 gap-4 md:col-span-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check In <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in_date" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check Out <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out_date" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                <select name="room_type_id" class="w-full rounded-xl border-slate-200 px-4 py-3 border focus:ring-primary focus:border-primary transition-colors" required>
                    <option value="">Select a room...</option>
                    @foreach($roomTypes ?? [] as $room)
                        <option value="{{ $room->id }}" data-price-night="{{ $room->price_per_night }}" data-price-month="{{ $room->price_per_month }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Guests</label>
                    <input type="number" name="num_guests" min="1" value="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Stay Type</label>
                    <select name="stay_type" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        <option value="short_term">Short Term</option>
                        <option value="long_term">Long Term</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Rate Fields -->
            <div id="rate-per-night-wrapper">
                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Night <span class="text-red-500">*</span></label>
                <input type="number" name="rate_per_night" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
            </div>

            <div id="rate-per-month-wrapper" class="hidden">
                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Month <span class="text-red-500">*</span></label>
                <input type="number" name="rate_per_month" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Total Amount <span class="text-red-500">*</span></label>
                    <input type="number" name="total_amount" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Deposit Amount</label>
                    <input type="number" name="deposit_amount" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Amount Paid</label>
                    <input type="number" name="amount_paid" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Payment Status</label>
                    <select name="payment_status" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
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
                <textarea name="special_requests" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. extra pillows, late check-in..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Admin Notes (Internal)</label>
                <textarea name="admin_notes" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Internal notes about this booking..."></textarea>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="w-full md:w-auto bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                Save Booking
            </button>
        </div>
    </form>
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
                window.location.href = "{{ route('admin.bookings') }}";
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
        const roomTypeSelect = document.querySelector('select[name="room_type_id"]');
        const checkInInput = document.querySelector('input[name="check_in_date"]');
        const checkOutInput = document.querySelector('input[name="check_out_date"]');
        
        const ratePerNightWrapper = document.getElementById('rate-per-night-wrapper');
        const ratePerMonthWrapper = document.getElementById('rate-per-month-wrapper');
        const ratePerNightInput = document.querySelector('input[name="rate_per_night"]');
        const ratePerMonthInput = document.querySelector('input[name="rate_per_month"]');
        const totalAmountInput = document.querySelector('input[name="total_amount"]');
        
        function calculateTotal() {
            if (!roomTypeSelect.value) return;

            const selectedRoom = roomTypeSelect.options[roomTypeSelect.selectedIndex];
            const priceNight = parseFloat(selectedRoom.dataset.priceNight) || 0;
            const priceMonth = parseFloat(selectedRoom.dataset.priceMonth) || 0;

            if (stayTypeSelect.value === 'short_term') {
                ratePerNightInput.value = Math.round(priceNight);
                
                const checkInDate = new Date(checkInInput.value);
                const checkOutDate = new Date(checkOutInput.value);
                
                if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
                    const diffTime = Math.abs(checkOutDate - checkInDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    totalAmountInput.value = Math.round(priceNight * diffDays);
                }
            } else { // long_term
                ratePerMonthInput.value = Math.round(priceMonth);
                totalAmountInput.value = Math.round(priceMonth);
            }
        }

        if (stayTypeSelect && ratePerNightWrapper && ratePerMonthWrapper && roomTypeSelect) {
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
                calculateTotal();
            }

            toggleRateFields();
            stayTypeSelect.addEventListener('change', toggleRateFields);
            
            roomTypeSelect.addEventListener('change', calculateTotal);
            checkInInput.addEventListener('change', calculateTotal);
            checkOutInput.addEventListener('change', calculateTotal);
        }
    });
</script>
@endpush
