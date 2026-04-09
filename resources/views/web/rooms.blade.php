@extends('layouts.app')

@section('title', 'Rooms - DwellCasa')

@section('content')
<section class="py-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Our Rooms</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Choose from our selection of beautifully designed rooms and suites, each offering comfort and luxury.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($roomTypes as $roomType)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-slate-100">
                <div class="h-56 bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                    <span class="text-slate-600 font-medium">Room Image</span>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-serif italic font-semibold text-slate-900 mb-3">{{ $roomType->name }}</h3>
                    <p class="text-slate-700 mb-6">{{ $roomType->description }}</p>
                    <div class="space-y-2 mb-6 text-slate-700">
                        <p><strong>Max Occupancy:</strong> {{ $roomType->max_occupancy }} guests</p>
                        <p><strong>Size:</strong> {{ $roomType->size_sqft }} sq ft</p>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                        <div>
                            <span class="text-3xl font-bold text-blue-600">
                                ${{ $roomType->price_per_night }}
                            </span>
                            <span class="text-slate-600">/night</span>
                            @if($roomType->price_per_month)
                            <p class="text-sm text-slate-600 mt-1">${{ $roomType->price_per_month }}/month</p>
                            @endif
                        </div>
                        <a href="{{ route('booking.create') }}?room_type={{ $roomType->id }}" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all text-center">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection