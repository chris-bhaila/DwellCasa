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
                            <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Date *</label>
                            <input type="date" name="check_out_date" value="{{ old('check_out_date') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
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
                            <select name="room_type_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }} - ${{ $roomType->price_per_night }}/night
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-10">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Additional Information</h2>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Special Requests</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Any special requests or additional information...">{{ old('message') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-4 rounded-lg font-bold text-lg hover:shadow-lg transition-all">
                        Submit Booking Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection