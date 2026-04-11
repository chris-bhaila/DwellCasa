@extends('layouts.app')

@section('title', ($roomType->name ?? 'Luxury Suite') . ' - DwellCasa')

@section('content')
<!-- Hero Image Section -->
<section class="relative h-[60vh] min-h-[400px] flex items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=1920"
            class="w-full h-full object-cover opacity-60" alt="Room Hero">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-900/60"></div>
    </div>

    <div class="relative z-10 text-center text-white px-6 mt-16" data-aos="fade-up">
        <span class="font-serif uppercase tracking-[0.2em] text-sm mb-4 block font-medium">DwellCasa Signature</span>
        <h1 class="font-serif text-5xl md:text-7xl font-bold italic mb-4">{{ $roomType->name ?? 'Executive Suite' }}</h1>
        <div class="flex items-center justify-center gap-4 text-sm font-light tracking-widest uppercase">
            <span>{{ $roomType->size_sqft ?? '450' }} sq ft</span>
            <span class="text-primary font-bold">•</span>
            <span>Up to {{ $roomType->max_occupancy ?? '2' }} Guests</span>
        </div>
    </div>
</section>

<section class="py-16 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Image Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-16" data-aos="fade-up">
            <div class="md:col-span-2 aspect-[16/9] md:aspect-[2/1] rounded-[2rem] overflow-hidden cursor-pointer group shadow-sm">
                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=1200"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Room View">
            </div>
            <div class="grid grid-cols-2 md:grid-cols-1 gap-4">
                <div class="aspect-square md:aspect-auto md:h-full rounded-[2rem] overflow-hidden cursor-pointer group shadow-sm">
                    <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&q=80&w=600"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Bathroom">
                </div>
                <div class="aspect-square md:aspect-auto md:h-full rounded-[2rem] overflow-hidden cursor-pointer group relative shadow-sm">
                    <img src="https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=600"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Details">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center transition-colors group-hover:bg-black/50">
                        <span class="text-white font-semibold tracking-wide flex items-center gap-2">View All Photos <span>→</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Description -->
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-6">About this Space</h2>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        {{ $roomType->description ?? "Experience unparalleled luxury and comfort in our meticulously designed suite. Blending contemporary elegance with subtle traditional touches, this room offers a serene sanctuary above the bustling city. Large windows frame stunning views, while the plush bedding ensures a restful night's sleep." }}
                    </p>
                </div>

                <!-- Amenities -->
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-6">What this room offers</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        @forelse($roomType->amenities ?? [] as $amenity)
                            @if ($amenity->is_active == 1)
                                <div class="flex items-center gap-3 text-slate-700">
                                    <span class="text-2xl text-black">{!! $amenity->icon ?: '✨' !!}</span>
                                    <span class="font-medium">{{ $amenity->name }}</span>
                                </div>
                            @endif
                        @empty
                                <p class="text-slate-500 italic col-span-full">No specific amenities listed for this room.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar / Sticky Booking Card -->
            <div class="lg:col-span-1" data-aos="fade-left" data-aos-delay="200">
                <div class="sticky top-32 bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100">

                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Starting From</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-serif font-bold text-slate-900" id="bw-rate-display">
                                Rs. {{ number_format($roomType->price_per_night ?? 15000, 0) }}
                            </span>
                            <span class="text-slate-500 font-medium">/night</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1 font-medium" id="bw-nights-label"></p>
                    </div>

                    <!-- Date + Guest Fields -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden mb-4">
                        <div class="grid grid-cols-2 divide-x divide-slate-200">
                            <div class="p-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Check-in</p>
                                <input id="bw-checkin" type="text" placeholder="MM/DD/YYYY" readonly
                                    class="text-sm text-slate-800 w-full outline-none cursor-pointer bg-transparent placeholder-slate-300">
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Checkout</p>
                                <input id="bw-checkout" type="text" placeholder="MM/DD/YYYY" readonly
                                    class="text-sm text-slate-800 w-full outline-none cursor-pointer bg-transparent placeholder-slate-300">
                            </div>
                        </div>
                        <div class="border-t border-slate-200 p-3 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Guests</p>
                                <select id="bw-guests" class="text-sm text-slate-800 bg-transparent outline-none cursor-pointer">
                                    @for ($i = 1; $i <= ($roomType->max_occupancy ?? 4); $i++)
                                        <option value="{{ $i }}">{{ $i }} guest{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                </select>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div id="bw-summary" class="hidden bg-slate-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span id="bw-rate-label"></span>
                            <span id="bw-subtotal"></span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-900 pt-2 border-t border-slate-200">
                            <span>Total</span>
                            <span id="bw-total"></span>
                        </div>
                    </div>

                    <!-- Cancellation note -->
                    <p id="bw-cancel-msg" class="hidden text-xs text-slate-500 text-center mb-4">
                        Free cancellation before <span id="bw-cancel-date" class="underline"></span>
                    </p>

                    <!-- Hidden form -->
                    <form id="bw-form" action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                        <input type="hidden" name="stay_type" value="short_term">
                        <input type="hidden" name="rate_per_night" value="{{ $roomType->price_per_night ?? 15000 }}">
                        <input type="hidden" name="check_in_date" id="bw-form-checkin">
                        <input type="hidden" name="check_out_date" id="bw-form-checkout">
                        <input type="hidden" name="num_guests" id="bw-form-guests">
                        <input type="hidden" name="total_amount" id="bw-form-total">

                        <button type="submit" id="bw-btn" disabled
                            class="block w-full bg-primary text-white text-center px-6 py-4 rounded-xl font-bold tracking-wide 
                       transition-all transform hover:-translate-y-1
                       disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed disabled:transform-none
                       hover:bg-primary-dark hover:shadow-lg">
                            Reserve Now
                        </button>
                    </form>

                    <p class="text-center text-xs text-slate-400 mt-4 font-medium tracking-wide">
                        You won't be charged yet
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
@push('scripts')
<script>
(function () {
    const RATE = {{ $roomType->price_per_night ?? 15000 }};
    const BOOKED = @json($bookedDates ?? []);

    let checkInDate = null;
    let checkOutDate = null;
    let checkoutPicker = null;

    const checkinInput  = document.getElementById('bw-checkin');
    const checkoutInput = document.getElementById('bw-checkout');
    const btn           = document.getElementById('bw-btn');
    const summary       = document.getElementById('bw-summary');
    const rateLabel     = document.getElementById('bw-rate-label');
    const subtotalEl    = document.getElementById('bw-subtotal');
    const totalEl       = document.getElementById('bw-total');
    const nightsLabel   = document.getElementById('bw-nights-label');
    const cancelMsg     = document.getElementById('bw-cancel-msg');
    const cancelDate    = document.getElementById('bw-cancel-date');

    function formatShort(d) {
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    function updateSummary() {
        if (!checkInDate || !checkOutDate) {
            summary.classList.add('hidden');
            cancelMsg.classList.add('hidden');
            nightsLabel.textContent = '';
            btn.disabled = true;
            return;
        }

        const nights   = Math.round((checkOutDate - checkInDate) / 86400000);
        const subtotal = nights * RATE;

        rateLabel.textContent  = 'Rs. ' + RATE.toLocaleString() + ' x ' + nights + ' night' + (nights !== 1 ? 's' : '');
        subtotalEl.textContent = 'Rs. ' + subtotal.toLocaleString();
        totalEl.textContent    = 'Rs. ' + subtotal.toLocaleString();
        nightsLabel.textContent = nights + ' night' + (nights !== 1 ? 's' : '') + ' total';

        const cancelD = new Date(checkInDate);
        cancelD.setDate(cancelD.getDate() - 1);
        cancelDate.textContent = formatShort(cancelD);

        document.getElementById('bw-form-checkin').value = checkInDate.toISOString().split('T')[0];
        document.getElementById('bw-form-checkout').value = checkOutDate.toISOString().split('T')[0];
        document.getElementById('bw-form-guests').value = document.getElementById('bw-guests').value;
        document.getElementById('bw-form-total').value = subtotal;

        summary.classList.remove('hidden');
        cancelMsg.classList.remove('hidden');
        btn.disabled = false;
    }

    flatpickr(checkinInput, {
        minDate: 'today',
        dateFormat: 'm/d/Y',
        disable: BOOKED,
        disableMobile: true,
        onChange: function (selected) {
            checkInDate = selected[0] || null;
            if (checkInDate && checkOutDate && checkOutDate <= checkInDate) {
                checkOutDate = null;
                checkoutPicker.clear();
            }
            if (checkoutPicker) {
                checkoutPicker.set('minDate', checkInDate
                    ? new Date(checkInDate.getTime() + 86400000)
                    : 'today');
            }
            updateSummary();
        }
    });

    checkoutPicker = flatpickr(checkoutInput, {
        minDate: 'today',
        dateFormat: 'm/d/Y',
        disable: BOOKED,
        disableMobile: true,
        onChange: function (selected) {
            checkOutDate = selected[0] || null;
            updateSummary();
        }
    });

    document.getElementById('bw-guests').addEventListener('change', updateSummary);
})();
</script>
@endpush
@endsection