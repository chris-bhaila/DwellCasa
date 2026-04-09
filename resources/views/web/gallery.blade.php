@extends('layouts.app')

@section('title', 'Gallery - DwellCasa')

@section('content')
<section class="py-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Photo Gallery</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Explore our beautiful property through our curated collection of photos.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($images as $image)
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-shadow border border-slate-200">
                <div class="aspect-square bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                    <span class="text-slate-600 font-medium">{{ $image->alt_text ?: 'Gallery Image' }}</span>
                </div>
                @if($image->caption)
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition duration-300 flex items-end">
                    <p class="text-white p-4 opacity-0 group-hover:opacity-100 transition duration-300">
                        {{ $image->caption }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection