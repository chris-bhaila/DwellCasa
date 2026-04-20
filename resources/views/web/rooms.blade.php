@extends('layouts.app')

@section('title', 'Rooms - DwellCasa')

@section('content')
<section class="pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Our Rooms</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Choose from our selection of beautifully designed rooms and suites, each offering comfort and luxury.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($roomTypes as $roomType)
            <a href="{{ route('web.rooms.show', $roomType->id) }}" class="block bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-slate-100 group">
                <div class="block h-56 overflow-hidden bg-slate-200">
                    @php
                        $roomImage = $roomType->galleryImages->first();
                        $imageUrl = $roomType->thumbnail 
                        ? asset('storage/' . $roomType->thumbnail)
                        : ($roomImage
                        ? (filter_var($roomImage->filename, FILTER_VALIDATE_URL)
                        ? $roomImage->filename
                        : asset('storage/' . ltrim($roomImage->filename, '/')))
                        : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=800');
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $roomType->name }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-serif italic font-semibold text-slate-900 mb-3 group-hover:text-primary transition-colors">
                        {{ $roomType->name }}
                    </h3>
                    <p class="text-slate-700 mb-6">{{ $roomType->description }}</p>
                    <div class="space-y-2 mb-6 text-slate-700">
                        <p><strong>Max Occupancy:</strong> {{ $roomType->max_occupancy }} guests</p>
                        <p><strong>Size:</strong> {{ $roomType->size_sqft }} sq ft</p>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                        <div>
                            <span class="text-3xl font-bold text-primary">
                                Rs. {{ number_format($roomType->price_per_night, 0) }}
                            </span>
                            <span class="text-slate-600">/night</span>
                            @if($roomType->price_per_month)
                            <p class="text-sm text-slate-600 mt-1">Rs. {{ number_format($roomType->price_per_month, 0) }}/month</p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection