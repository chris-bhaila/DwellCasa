@extends('layouts.app')

@section('title', 'Gallery - DwellCasa')

@section('content')
@php
    // Filter out room-specific images so the loop indices match exactly for the JS Lightbox
    $webImages = collect($images)->reject(fn($img) => $img->imageable_type === 'App\Models\RoomType')->values();
@endphp
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl !font-sans font-bold text-slate-900 mb-4">{{ $webInfo->gallery_heading }}</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                {{ $webInfo->gallery_sub_heading }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($webImages as $index => $image)
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-shadow border border-slate-200">
                <div class="aspect-square overflow-hidden bg-slate-200">
                    @php
                        $imageUrl = filter_var($image->filename, FILTER_VALIDATE_URL) ? $image->filename : asset('storage/' . ltrim($image->filename, '/'));
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $image->alt_text ?: 'Gallery Image' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 cursor-pointer" onclick="openLightbox({{ $index }})">
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

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <!-- Controls -->
    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <div id="lightbox-counter" class="text-white font-medium text-sm"></div>
        <button onclick="closeLightbox()" class="text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full shadow-sm focus:outline-none">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Image Container -->
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center p-4 md:p-12 overflow-hidden touch-pan-y gap-4 md:gap-8">
        <button onclick="prevImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">
        
        <button onclick="nextImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Caption -->
    <div class="absolute bottom-0 left-0 w-full p-6 text-center z-10 bg-gradient-to-t from-black/80 to-transparent">
        <h3 id="lightbox-caption" class="text-white text-lg font-serif italic mb-1"></h3>
        <p id="lightbox-category" class="text-primary text-xs font-bold uppercase tracking-wider"></p>
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
        
        setTimeout(() => {
            lightbox.classList.remove('opacity-0');
        }, 10);
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

        // Check if URL is absolute (like unsplash), otherwise use storage path
        lightboxImg.src = img.filename.match(/^https?:\/\//) ? img.filename : '{{ asset("storage") }}/' + img.filename;
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

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (lightbox && !lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    });

    // Touch Swipe Logic
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