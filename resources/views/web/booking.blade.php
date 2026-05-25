@extends('layouts.app')

@section('title', 'Book Your Stay - DwellCasa')

@section('content')
@push('head')
<style>
    /* Strikethrough ONLY for fully booked dates in flatpickr */
    .flatpickr-day.fully-booked-date {
        text-decoration: line-through;
        color: #cbd5e1 !important;
    }
</style>
@endpush
<section class="py-16 md:py-24 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Complete Your Booking</h1>
            <p class="text-lg text-slate-700">
                Please provide your details below to finalize your reservation.
            </p>
        </div>

        <form action="{{ route('booking.store', $location->slug) }}" method="POST" id="main-booking-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                <!-- Left Column: Guest Details -->
                <div class="lg:col-span-2 space-y-10">

                    <!-- Info / pending verification -->
                    @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl shadow-sm">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Verification email sent</h3>
                                <p class="mt-1 text-sm text-blue-700">{{ session('info') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

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
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" placeholder=" " class="peer w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all [&:not(:placeholder-shown):invalid]:border-red-500 [&:not(:placeholder-shown):invalid]:ring-red-200" pattern="^[a-zA-Z\s]{2,255}$" title="Please enter a valid name (letters and spaces only)" required>
                                <p class="mt-2 hidden text-xs text-red-500 peer-[&:not(:placeholder-shown):invalid]:block">Please enter a valid name (letters and spaces only).</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Email *</label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}" placeholder=" " class="peer w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all [&:not(:placeholder-shown):invalid]:border-red-500 [&:not(:placeholder-shown):invalid]:ring-red-200" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$" title="Please enter a valid email address" required>
                                <p class="mt-2 hidden text-xs text-red-500 peer-[&:not(:placeholder-shown):invalid]:block">Please enter a valid email address.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold tracking-widest uppercase text-slate-500 mb-2">Phone</label>
                                <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" placeholder=" " class="peer w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all [&:not(:placeholder-shown):invalid]:border-red-500 [&:not(:placeholder-shown):invalid]:ring-red-200" pattern="^\+?[0-9\s\-]{7,15}$" title="Please enter a valid phone number">
                                <p class="mt-2 hidden text-xs text-red-500 peer-[&:not(:placeholder-shown):invalid]:block">Please enter a valid phone number (7-15 digits).</p>
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
                                    <select id="room_type_id" name="room_type_id" class="w-full text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg p-3 pointer-events-none cursor-not-allowed" tabindex="-1" required>
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
                                        <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date', request('check_in_date')) }}" class="w-full text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg p-3 pointer-events-none cursor-not-allowed" required readonly>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Check-out</label>
                                        <input type="date" id="check_out_date" name="check_out_date" value="{{ old('check_out_date', request('check_out_date')) }}" class="w-full text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg p-3 pointer-events-none cursor-not-allowed" required readonly>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Guests</label>
                                        <select name="num_guests" class="w-full text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg p-3 pointer-events-none cursor-not-allowed" tabindex="-1" required>
                                            @for($i = 1; $i <= 4; $i++)
                                                <option value="{{ $i }}" {{ old('num_guests', request('num_guests')) == $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Stay Type</label>
                                        <select name="stay_type" class="w-full text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg p-3 pointer-events-none cursor-not-allowed" tabindex="-1" required>
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

                            <button type="submit" class="w-full cursor-pointer bg-primary text-white px-6 py-4 rounded-xl font-bold tracking-wide uppercase text-sm hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-lg opacity-50 cursor-not-allowed" disabled>
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
        const checkInInput = document.getElementById('check_in_date'); // Original input
        const checkOutInput = document.getElementById('check_out_date'); // Original input
        const roomTypeSelect = document.getElementById('room_type_id');

        const summarySection = document.getElementById('booking-summary');
        const summaryRate = document.getElementById('summary-rate');
        const summaryNights = document.getElementById('summary-nights');
        const summaryTotal = document.getElementById('summary-total');
        const submitButton = document.querySelector('#main-booking-form button[type="submit"]');

        let checkInFlatpickr;
        let checkOutFlatpickr;
        let BOOKED_DATES = [];

        function toLocalYMD(date) {
            return date.toISOString().split('T')[0];
        }

        async function calculateTotal() {
            if (!checkInInput || !checkOutInput || !roomTypeSelect) return;

            const checkInDate = new Date(checkInInput.value);
            const checkOutDate = new Date(checkOutInput.value);
            const selectedOption = roomTypeSelect.options[roomTypeSelect.selectedIndex];
            const ratePerNight = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const ratePerMonth = parseFloat(selectedOption.getAttribute('data-price-month')) || 0;
            const stayType = document.querySelector('select[name="stay_type"]').value;

            let isValidSelection = false;
            if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
                const diffDays = Math.floor((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                let rate, total, durationText;

                if (stayType === 'long_term') {
                    rate = ratePerMonth;
                    const months = Math.ceil(diffDays / 30);
                    total = rate * months;
                    durationText = months + (months === 1 ? ' month' : ' months');
                } else {
                    rate = ratePerNight;
                    total = rate * diffDays;
                    durationText = diffDays + (diffDays === 1 ? ' night' : ' nights');
                }

                if (rate > 0) {
                    // Check if any date in the selected range is fully booked
                    let isRangeBooked = false;
                    const current = new Date(checkInDate);
                    while (current < checkOutDate) {
                        const dateStr = toLocalYMD(current);
                        if (BOOKED_DATES.includes(dateStr)) {
                            isRangeBooked = true;
                            break;
                        }
                        current.setDate(current.getDate() + 1);
                    }
                    isValidSelection = !isRangeBooked;
                }
                if (isValidSelection) {
                    summarySection.classList.remove('hidden');
                    summaryRate.textContent = 'Rs. ' + rate.toLocaleString();
                    summaryNights.textContent = durationText;
                    summaryTotal.textContent = 'Rs. ' + total.toLocaleString();
                } else {
                    summarySection.classList.add('hidden');
                }
            } else {
                summarySection.classList.add('hidden');
            }

            if (isValidSelection) {
                submitButton.removeAttribute('disabled');
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitButton.setAttribute('disabled', 'disabled');
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        async function fetchAndApplyAvailability(roomTypeId) {
            if (!roomTypeId) {
                BOOKED_DATES = [];
                updateFlatpickrDisableDates();
                return;
            }

            const today = new Date();
            const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
            const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1);
            const nextMonthStr = nextMonth.getFullYear() + '-' + String(nextMonth.getMonth() + 1).padStart(2, '0');

            let allUnavailable = [];
            try {
                const responseCurrent = await fetch(`/api/room-types/${roomTypeId}/availability?month=${currentMonth}`);
                const jsonCurrent = await responseCurrent.json();
                allUnavailable = allUnavailable.concat(jsonCurrent.data.fully_booked_dates ?? []);

                const responseNext = await fetch(`/api/room-types/${roomTypeId}/availability?month=${nextMonthStr}`);
                const jsonNext = await responseNext.json();
                allUnavailable = allUnavailable.concat(jsonNext.data.fully_booked_dates ?? []);

            } catch (e) {
                console.error('Error fetching availability:', e);
            }
            BOOKED_DATES = [...new Set(allUnavailable)].sort(); // Remove duplicates and sort
            updateFlatpickrDisableDates();
        }

        function updateFlatpickrDisableDates() {
            if (checkInFlatpickr) {
                checkInFlatpickr.set('disable', [
                    function(date) {
                        return BOOKED_DATES.includes(toLocalYMD(date));
                    }
                ]);
                checkInFlatpickr.redraw(); // Important to redraw the calendar
            }
            if (checkOutFlatpickr) {
                checkOutFlatpickr.set('disable', [
                    function(date) {
                        return BOOKED_DATES.includes(toLocalYMD(date));
                    }
                ]);
                checkOutFlatpickr.redraw();
            }
            calculateTotal(); // Re-evaluate the summary and button state after dates are updated
        }

        // Initialize Flatpickr for check-in
        checkInFlatpickr = flatpickr(checkInInput, {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            clickOpens: false,
            disable: [
                function(date) {
                    return BOOKED_DATES.includes(toLocalYMD(date));
                }
            ], // Initially empty, will be updated
            disableMobile: true,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const localDate = toLocalYMD(dayElem.dateObj);
                if (BOOKED_DATES.includes(localDate)) {
                    dayElem.classList.add('fully-booked-date');
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                const selectedCheckIn = selectedDates[0];
                if (selectedCheckIn) {
                    const minCheckoutDate = new Date(selectedCheckIn.getTime() + 86400000);
                    checkOutFlatpickr.set('minDate', minCheckoutDate);

                    let maxCheckoutDate = null;
                    const checkInYMD = toLocalYMD(selectedCheckIn);
                    for (let i = 0; i < BOOKED_DATES.length; i++) {
                        if (BOOKED_DATES[i] > checkInYMD) {
                            maxCheckoutDate = BOOKED_DATES[i];
                            break;
                        }
                    }
                    checkOutFlatpickr.set('maxDate', maxCheckoutDate);

                    if (checkOutInput.value && (new Date(checkOutInput.value) < minCheckoutDate || (maxCheckoutDate && new Date(checkOutInput.value) > new Date(maxCheckoutDate)))) {
                        checkOutFlatpickr.clear();
                    }
                } else {
                    checkOutFlatpickr.set('minDate', 'today');
                    checkOutFlatpickr.set('maxDate', null);
                    checkOutFlatpickr.clear();
                }
                calculateTotal();
            }
        });

        // Initialize Flatpickr for check-out
        checkOutFlatpickr = flatpickr(checkOutInput, {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            clickOpens: false,
            disable: [
                function(date) {
                    return BOOKED_DATES.includes(toLocalYMD(date));
                }
            ], // Initially empty, will be updated
            disableMobile: true,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const localDate = toLocalYMD(dayElem.dateObj);
                if (BOOKED_DATES.includes(localDate)) {
                    dayElem.classList.add('fully-booked-date');
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                calculateTotal();
            }
        });

        // Event listeners for changes
        roomTypeSelect.addEventListener('change', function() {
            const selectedRoomTypeId = this.value;
            fetchAndApplyAvailability(selectedRoomTypeId);
            checkInFlatpickr.clear();
            checkOutFlatpickr.clear();
            calculateTotal();
        });

        document.querySelector('select[name="num_guests"]').addEventListener('change', calculateTotal);
        document.querySelector('select[name="stay_type"]').addEventListener('change', calculateTotal);

        // Initial fetch and apply availability based on pre-selected room type (if any)
        const initialRoomTypeId = roomTypeSelect.value;
        if (initialRoomTypeId) {
            fetchAndApplyAvailability(initialRoomTypeId);
        } else {
            calculateTotal(); // Ensure button is disabled if no room type is selected
        }

        document.getElementById('main-booking-form').addEventListener('submit', function(e) {
            // If the button is disabled, prevent submission
            if (submitButton.disabled) {
                e.preventDefault();
                alert('Please select valid and available check-in/check-out dates.');
            }
        });
    });
</script>
@endpush
@endsection