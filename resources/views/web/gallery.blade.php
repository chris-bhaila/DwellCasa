@extends('layouts.app')

@section('title', 'Gallery - DwellCasa')

@section('content')
<section class="pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">{{ $webInfo->gallery_heading }}</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                {{ $webInfo->gallery_sub_heading }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($images as $image)
            @if($image->imageable_type === 'App\Models\RoomType')
                @continue
            @endif
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-shadow border border-slate-200">
                <div class="aspect-square overflow-hidden bg-slate-200">
                    @php
                        $imageUrl = filter_var($image->filename, FILTER_VALIDATE_URL) ? $image->filename : asset('storage/' . ltrim($image->filename, '/'));
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $image->alt_text ?: 'Gallery Image' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
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