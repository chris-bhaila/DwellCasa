@extends('layouts.app')

@section('title', 'Book Your Stay - DwellCasa')

@section('content')
<section class="py-16 md:py-24 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Complete Your Booking</h1>
            <p class="text-lg text-slate-700">
                Please provide your details below to finalize your reservation.
            </p>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" id="main-booking-form">
            @csrf

            <!-- Hidden fields for calculated amounts -->
            <input type="hidden" name="rate_per_night" id="input_rate_per_night" value="{{ request('rate_per_night') }}">
            <input type="hidden" name="rate_per_month" id="input_rate_per_month">
            <input type="hidden" name="total_amount" id="input_total_amount" value="{{ request('total_amount') }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Left Column: Guest Details -->
                <div class="lg:col-span-2 space-y-10">
                    
                    <!-- Validation Errors -->
                    @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were some errors with your submission:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Guest Information -->
                    <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-slate-100">
                        <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Guest Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Full Name *</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Email *</label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Phone</label>
                                <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-slate-100">
                        <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Additional Information</h2>
                        <div>
                            <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Special Requests</label>
                            <textarea name="special_requests" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="Any special requests, dietary requirements, or arrival time...">{{ old('special_requests') }}</textarea>
                        </div>
                </div>

                <!-- Right Column: Booking Details & Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-32 bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100">
                        <h3 class="text-2xl font-serif italic font-bold text-slate-900 mb-6">Your Stay</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Room Type</label>
                                <select id="room_type_id" name="room_type_id" class="w-full text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:ring-primary focus:border-primary" required>
                                    <option value="" data-price="0" data-price-month="0">Select Room Type</option>
                                    @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" data-price="{{ $roomType->price_per_night }}" data-price-month="{{ $roomType->price_per_month ?? 0 }}" {{ old('room_type_id', request('room_type_id')) == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Check-in</label>
                                    <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date', request('check_in_date')) }}" class="w-full text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:ring-primary focus:border-primary" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Check-out</label>
                                    <input type="date" id="check_out_date" name="check_out_date" value="{{ old('check_out_date', request('check_out_date')) }}" class="w-full text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:ring-primary focus:border-primary" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Guests</label>
                                    <select name="num_guests" class="w-full text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:ring-primary focus:border-primary" required>
                                        @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('num_guests', request('num_guests')) == $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Stay Type</label>
                                    <select name="stay_type" class="w-full text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:ring-primary focus:border-primary" required>
                                        <option value="short_term" {{ old('stay_type', request('stay_type')) == 'short_term' ? 'selected' : '' }}>Short Term</option>
                                        <option value="long_term" {{ old('stay_type', request('stay_type')) == 'long_term' ? 'selected' : '' }}>Long Term</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Calculation Box -->
                        <div id="booking-summary" class="bg-slate-50 rounded-xl p-5 mb-6 space-y-3 border border-slate-100 hidden">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Rate per night</span>
                                <span class="font-semibold text-slate-900" id="summary-rate">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Duration</span>
                                <span class="font-semibold text-slate-900" id="summary-nights">0 nights</span>
                            </div>
                            <div class="pt-3 border-t border-slate-200 flex justify-between items-end">
                                <span class="text-sm font-bold uppercase tracking-widest text-slate-400">Total</span>
                                <span class="text-2xl font-serif font-bold text-primary" id="summary-total">Rs. 0</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white px-6 py-4 rounded-xl font-bold tracking-wide uppercase text-sm hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-lg">
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('check_in_date');
        const checkOutInput = document.getElementById('check_out_date');
        const roomTypeSelect = document.getElementById('room_type_id');

        const summarySection = document.getElementById('booking-summary');
        const summaryRate = document.getElementById('summary-rate');
        const summaryNights = document.getElementById('summary-nights');
        const summaryTotal = document.getElementById('summary-total');

        const inputRatePerNight = document.getElementById('input_rate_per_night');
        const inputRatePerMonth = document.getElementById('input_rate_per_month');
        const inputTotalAmount = document.getElementById('input_total_amount');

        function calculateTotal() {
            if (!checkInInput || !checkOutInput || !roomTypeSelect) return;

            const checkInDate = new Date(checkInInput.value);
            const checkOutDate = new Date(checkOutInput.value);
            const selectedOption = roomTypeSelect.options[roomTypeSelect.selectedIndex];
            const ratePerNight = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const ratePerMonth = parseFloat(selectedOption.getAttribute('data-price-month')) || 0;

            if (checkInInput.value && checkOutInput.value && ratePerNight > 0 && checkOutDate > checkInDate) {
                const diffTime = checkOutDate - checkInDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); 
                const totalAmount = ratePerNight * diffDays;

                summarySection.classList.remove('hidden');
                summaryRate.textContent = 'Rs. ' + ratePerNight.toLocaleString();
                summaryNights.textContent = diffDays + (diffDays === 1 ? ' night' : ' nights');
                summaryTotal.textContent = 'Rs. ' + totalAmount.toLocaleString();

                if (inputRatePerNight) inputRatePerNight.value = ratePerNight;
                if (inputRatePerMonth) inputRatePerMonth.value = ratePerMonth;
                if (inputTotalAmount) inputTotalAmount.value = totalAmount;
            } else {
                summarySection.classList.add('hidden');
            }
        }

        [checkInInput, checkOutInput, roomTypeSelect].forEach(el => el?.addEventListener('change', calculateTotal));
        
        checkInInput?.addEventListener('change', function() {
            if (this.value) {
                let [y, m, d] = this.value.split('-');
                let minDate = new Date(y, m - 1, d);
                minDate.setDate(minDate.getDate() + 1);
                
                // Fix formatting padding issue to ensure proper date string format (YYYY-MM-DD)
                const year = minDate.getFullYear();
                const month = String(minDate.getMonth() + 1).padStart(2, '0');
                const day = String(minDate.getDate()).padStart(2, '0');
                let minDateStr = `${year}-${month}-${day}`;
                
                checkOutInput.min = minDateStr;
                if (checkOutInput.value && checkOutInput.value <= this.value) {
                    checkOutInput.value = minDateStr;
                    calculateTotal();
                }
            }
        });

        // Trigger calculation on load in case parameters are populated via URL
        calculateTotal();
    });
</script>
@endpush
@endsection