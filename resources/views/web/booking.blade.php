@extends('layouts.app')

@section('title', 'Book Your Stay - DwellCasa')

@section('content')
<section class="py-20 bg-[#fbfbf9]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Book Your Stay</h1>
            <p class="text-lg text-slate-700">
                Fill out the form below to submit a booking inquiry. We'll get back to you shortly.
            </p>
        </div>

        <div class="bg-white p-12 rounded-2xl shadow-lg border border-slate-100">
            <form action="{{ route('booking.store') }}" method="POST">
                @csrf

                <!-- Hidden fields for calculated amounts -->
                <input type="hidden" name="rate_per_night" id="input_rate_per_night">
                <input type="hidden" name="rate_per_month" id="input_rate_per_month">
                <input type="hidden" name="total_amount" id="input_total_amount">

                <!-- Validation Errors -->
                @if ($errors->any())
                <div class="mb-10 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
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
                <div class="mb-10">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Guest Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Full Name *</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                            <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Number of Guests *</label>
                            <select name="num_guests" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                <option value="1" {{ old('num_guests') == '1' ? 'selected' : '' }}>1 Guest</option>
                                <option value="2" {{ old('num_guests') == '2' ? 'selected' : '' }}>2 Guests</option>
                                <option value="3" {{ old('num_guests') == '3' ? 'selected' : '' }}>3 Guests</option>
                                <option value="4" {{ old('num_guests') == '4' ? 'selected' : '' }}>4 Guests</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="mb-10">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Booking Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Check-in Date *</label>
                            <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Date *</label>
                            <input type="date" id="check_out_date" name="check_out_date" value="{{ old('check_out_date') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Stay Type *</label>
                            <select name="stay_type" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                <option value="short_term" {{ old('stay_type') == 'short_term' ? 'selected' : '' }}>Short Term</option>
                                <option value="long_term" {{ old('stay_type') == 'long_term' ? 'selected' : '' }}>Long Term</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Room Type *</label>
                            <select id="room_type_id" name="room_type_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                <option value="" data-price="0" data-price-month="0">Select Room Type</option>
                                @foreach($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" data-price="{{ $roomType->price_per_night }}" data-price-month="{{ $roomType->price_per_month ?? 0 }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                    {{ $roomType->name }} - ${{ $roomType->price_per_night }}/night
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <div id="booking-summary" class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-100 hidden">
                        <h3 class="text-xl font-serif italic font-bold text-slate-900 mb-4">Booking Summary</h3>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-slate-700">Rate per night:</span>
                            <span class="font-semibold text-slate-900" id="summary-rate">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-slate-700">Number of nights:</span>
                            <span class="font-semibold text-slate-900" id="summary-nights">0</span>
                        </div>
                        <div class="border-t border-blue-200 my-4 pt-4 flex justify-between items-center">
                            <span class="text-lg font-bold text-slate-900">Total Amount:</span>
                            <span class="text-2xl font-bold text-primary" id="summary-total">$0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-10">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Additional Information</h2>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Any special requests or additional information...">{{ old('special_requests') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-primary text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-primary-dark hover:shadow-lg transition-all">
                        Submit Booking Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

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

            if (checkInInput.value && checkOutInput.value && ratePerNight >= 0 && checkOutDate > checkInDate) {
                const diffTime = checkOutDate - checkInDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); // Ignores anything after decimal
                const totalAmount = ratePerNight * diffDays;

                summarySection.classList.remove('hidden');
                summaryRate.textContent = '$' + ratePerNight.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                summaryNights.textContent = diffDays;
                summaryTotal.textContent = '$' + totalAmount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (inputRatePerNight) inputRatePerNight.value = ratePerNight;
                if (inputRatePerMonth) inputRatePerMonth.value = ratePerMonth;
                if (inputTotalAmount) inputTotalAmount.value = totalAmount;
            } else {
                summarySection.classList.add('hidden');

                if (inputRatePerNight) inputRatePerNight.value = '';
                if (inputRatePerMonth) inputRatePerMonth.value = '';
                if (inputTotalAmount) inputTotalAmount.value = '';
            }
        }


        [checkInInput, checkOutInput, roomTypeSelect].forEach(el => el?.addEventListener('change', calculateTotal));
        checkInInput?.addEventListener('change', function() {
            if (this.value) {
                let [y, m, d] = this.value.split('-');
                let minDate = new Date(y, m - 1, d);
                minDate.setDate(minDate.getDate() + 1);
                let minDateStr = minDate.getFullYear() + '-' + String(minDate.getMonth() + 1).padStart(2, '0') + '-' + String(minDate.getDate()).padStart(2, '0');
                checkOutInput.min = minDateStr;
                if (checkOutInput.value && checkOutInput.value <= this.value) {
                    checkOutInput.value = minDateStr;
                    calculateTotal();
                }
            }
        });

        calculateTotal();

    });
</script>
@endsection