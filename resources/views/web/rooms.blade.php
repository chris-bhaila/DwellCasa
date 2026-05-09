@extends('layouts.app')

@section('title', 'Rooms - DwellCasa')

@section('content')
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="relative pb-24 px-6 sm:px-12 lg:px-24 xl:px-36 overflow-hidden">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl !font-sans font-bold text-slate-900 mb-4">Our Rooms</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Choose from our selection of beautifully designed rooms and suites, each offering comfort and luxury.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 lg:mx-40">
            @foreach($roomTypes as $roomType)
            <a href="{{ route('web.rooms.show', [$location->slug, $roomType->id]) }}"
               class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-shadow duration-500 overflow-hidden flex flex-col group">

                <!-- Image -->
                <div class="relative w-full h-56 overflow-hidden flex-shrink-0">
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

                <!-- Body -->
                <div class="flex-1 flex flex-col p-6">
                    <h3 class="font-serif italic font-bold text-3xl text-slate-900 mb-3 group-hover:text-primary transition-colors">
                        {{ $roomType->name }}
                    </h3>
                    @if($roomType->description)
                    <p class="text-slate-500 text-base leading-relaxed line-clamp-3 mb-4 flex-1">
                        {{ $roomType->description }}
                    </p>
                    @else
                    <div class="flex-1"></div>
                    @endif

                    <hr class="border-slate-100 mb-4">

                    <div class="flex flex-wrap gap-2 mb-5">
                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg">
                            <i class="bi bi-people text-primary text-xs"></i>
                            {{ $roomType->max_occupancy }} guests
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg">
                            <i class="bi bi-aspect-ratio text-primary text-xs"></i>
                            {{ $roomType->size_sqft }} sq ft
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-3">
                        <div>
                            <span class="text-[9px] font-bold tracking-widest uppercase text-slate-300 block mb-1">FROM</span>
                            <p class="font-serif italic font-bold text-3xl text-primary leading-none">
                                Rs. {{ number_format($roomType->price_per_night, 0) }}<span class="font-sans text-slate-400 font-normal text-xs not-italic"> /night</span>
                            </p>
                        </div>
                        <span class="inline-flex items-center justify-center bg-slate-900 text-white text-xs font-semibold px-5 py-2.5 rounded-xl group-hover:bg-primary transition-colors whitespace-nowrap">
                            View Details &rarr;
                        </span>
                    </div>
                </div>

            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
