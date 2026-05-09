@extends('layouts.app')

@section('title', 'Gallery - DwellCasa')

@section('content')
@php
    $webImages = collect($images)->reject(fn($img) =>
        $img->imageable_type === 'App\Models\RoomType'
    )->values();

    // Bento pattern repeats every 9 images:
    // [0]=hero(2x2), [1][2]=small, [3][4]=small, [5][6]=small,
    // [7]=wide(2x1), [8][9]=small ... then repeat
    $bentoPattern = [
        0 => 'hero',    // col-span-2 row-span-2
        1 => 'small',
        2 => 'small',
        3 => 'small',
        4 => 'small',
        5 => 'small',
        6 => 'small',
        7 => 'wide',    // col-span-2
        8 => 'small',
        9 => 'small',
    ];
@endphp

<section class="pt-16 pb-24 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-14">
            <h1 class="text-5xl md:text-6xl !font-sans font-bold text-slate-900 mb-4">
                {{ $webInfo->gallery_heading }}
            </h1>
            <div class="w-12 h-px bg-[#A89070] mx-auto mb-4"></div>
            <p class="text-lg text-slate-500 max-w-xl mx-auto">
                {{ $webInfo->gallery_sub_heading }}
            </p>
        </div>

        {{-- Bento Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 auto-rows-[200px]"
             id="gallery-grid">
            @foreach($webImages as $index => $image)
            @php
                $pos     = $index % 10;
                $pattern = $bentoPattern[$pos] ?? 'small';
                $imageUrl = filter_var($image->filename, FILTER_VALIDATE_URL)
                    ? $image->filename
                    : asset('storage/' . ltrim($image->filename, '/'));

                $spanClass = match($pattern) {
                    'hero' => 'col-span-2 row-span-2',
                    'wide' => 'col-span-2',
                    default => 'col-span-1',
                };
            @endphp
            <div class="group relative overflow-hidden rounded-2xl
                        bg-slate-200 cursor-pointer {{ $spanClass }}"
                 onclick="openLightbox({{ $index }})">

                <img src="{{ $imageUrl }}"
                     alt="{{ $image->alt_text ?: 'Gallery Image' }}"
                     class="w-full h-full object-cover transition-transform
                            duration-700 group-hover:scale-105">

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t
                            from-black/65 via-transparent to-transparent
                            opacity-0 group-hover:opacity-100
                            transition-opacity duration-300
                            flex flex-col justify-end p-4">
                    @if($image->category)
                    <span class="inline-flex w-fit text-[9px] font-bold
                                 uppercase tracking-widest text-white
                                 bg-[#A89070]/80 px-2 py-0.5 rounded-full mb-1">
                        {{ ucfirst($image->category) }}
                    </span>
                    @endif
                    @if($image->caption)
                    <p class="text-white text-sm font-medium line-clamp-2
                              {{ $pattern === 'hero' ? 'text-base' : 'text-xs' }}">
                        {{ $image->caption }}
                    </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Empty state --}}
        @if($webImages->isEmpty())
        <div class="text-center py-24">
            <p class="text-slate-400 italic">No gallery images yet.</p>
        </div>
        @endif

    </div>
</section>

{{-- Lightbox Modal — keep exactly as-is --}}
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <div id="lightbox-counter" class="text-white font-medium text-sm"></div>
        <button onclick="closeLightbox()" class="text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full shadow-sm focus:outline-none">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <button onclick="prevImage(event)" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full focus:outline-none shadow-sm">
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center px-16 md:px-20 py-4 overflow-hidden touch-pan-y">
        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">
    </div>
    <button onclick="nextImage(event)" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full focus:outline-none shadow-sm">
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
    <div class="absolute bottom-0 left-0 w-full p-6 text-center z-10 bg-gradient-to-t from-black/80 to-transparent">
        <h3 id="lightbox-caption" class="text-white text-lg font-serif italic mb-1"></h3>
        <p id="lightbox-category" class="text-[#A89070] text-xs font-bold uppercase tracking-wider"></p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const images = @json($webImages);
    let currentIndex = 0;

    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxCategory = document.getElementById('lightbox-category');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const imgContainer = document.getElementById('lightbox-img-container');

    window.openLightbox = function(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        setTimeout(() => { lightbox.classList.remove('opacity-0'); }, 10);
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        lightbox.classList.add('opacity-0');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    };

    window.nextImage = function(e) {
        if (e) e.stopPropagation();
        if (images.length === 0) return;
        currentIndex = (currentIndex + 1) % images.length;
        animateSlide('right');
    };

    window.prevImage = function(e) {
        if (e) e.stopPropagation();
        if (images.length === 0) return;
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        animateSlide('left');
    };

    function updateLightbox() {
        if (images.length === 0) return;
        const img = images[currentIndex];
        lightboxImg.src = img.filename.match(/^https?:\/\//)
            ? img.filename
            : '{{ asset("storage") }}/' + img.filename;
        lightboxImg.alt = img.alt_text || 'Gallery Image';
        if (lightboxCaption) lightboxCaption.textContent = img.caption || img.alt_text || '';
        if (lightboxCategory) lightboxCategory.textContent = img.category ? img.category.replace(/_/g, ' ') : '';
        if (lightboxCounter) lightboxCounter.textContent = `${currentIndex + 1} / ${images.length}`;
    }

    function animateSlide(direction) {
        lightboxImg.style.transform = `translateX(${direction === 'right' ? '20px' : '-20px'}) scale(0.98)`;
        lightboxImg.style.opacity = '0.5';
        setTimeout(() => {
            updateLightbox();
            lightboxImg.style.transform = 'translateX(0) scale(1)';
            lightboxImg.style.opacity = '1';
        }, 150);
    }

    document.addEventListener('keydown', (e) => {
        if (lightbox && !lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;

    if (imgContainer) {
        imgContainer.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        imgContainer.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        const threshold = 50;
        if (touchEndX < touchStartX - threshold) nextImage();
        if (touchEndX > touchStartX + threshold) prevImage();
    }
</script>
@endpush
