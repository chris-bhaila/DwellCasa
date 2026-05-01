@extends('layouts.app')

@section('title', 'Rooms - DwellCasa')

@section('content')
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="relative pb-24 px-4 sm:px-6 lg:px-22 text-center overflow-hidden">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Our Rooms</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Choose from our selection of beautifully designed rooms and suites, each offering comfort and luxury.
            </p>
        </div>

        <div class="space-y-16">
            @foreach($roomTypes as $roomType)
            <div class="flex flex-col md:flex-row {{ $loop->even ? 'md:flex-row-reverse' : '' }} items-stretch bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-shadow duration-500 overflow-hidden border border-slate-100 group">
                
                <!-- Image Section -->
                <div class="w-full md:w-1/2 relative overflow-hidden min-h-[300px] md:min-h-[400px]">
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
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                </div>

                <!-- Info Section -->
                <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center">
                    <h3 class="text-3xl md:text-4xl font-serif italic font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors">
                        {{ $roomType->name }}
                    </h3>
                    
                    @if($roomType->description)
                    <p class="text-slate-600 mb-8 leading-relaxed line-clamp-3">
                        {{ $roomType->description }}
                    </p>
                    @endif
                    
                    <div class="flex flex-wrap gap-x-8 gap-y-4 mb-8 text-sm text-slate-700">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Max Occupancy</span>
                            <span class="font-medium">{{ $roomType->max_occupancy }} guests</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Room Size</span>
                            <span class="font-medium">{{ $roomType->size_sqft }} sq ft</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between sm:items-end gap-6">
                        <div>
                            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Starting from</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-serif font-bold text-primary">Rs. {{ number_format($roomType->price_per_night, 0) }}</span>
                                <span class="text-slate-500 font-medium">/night</span>
                            </div>
                            @if($roomType->price_per_month)
                            <p class="text-sm text-slate-600 mt-1">Rs. {{ number_format($roomType->price_per_month, 0) }}/month</p>
                            @endif
                        </div>
                        <a href="{{ route('web.rooms.show', [$location->slug, $roomType->id]) }}" class="inline-flex items-center justify-center bg-slate-900 text-white px-8 py-3.5 rounded-xl font-medium hover:bg-primary transition-colors hover:-translate-y-0.5 transform duration-200">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection