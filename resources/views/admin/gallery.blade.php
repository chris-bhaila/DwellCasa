@extends('layouts.admin')

@section('title', 'Gallery Management - DwellCasa Admin')
@section('header_title', 'Gallery')

@section('content')

@php
$categories = ['interior', 'exterior', 'dining', 'amenities', 'other'];
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Gallery Management</h1>
        <p class="text-slate-500 mt-1">Manage images across different categories for your property.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="
            document.getElementById('upload-modal').classList.remove('hidden');
            document.getElementById('upload-modal').classList.add('flex');
        " class="relative inline-flex items-center justify-center bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm group">
            <i class="bi bi-cloud-arrow-up mr-2 text-lg"></i>
            Upload Image
        </button>
    </div>
</div>

<!-- Filters -->
<div class="flex flex-wrap gap-2 mb-8">
    <button class="filter-btn active px-4 py-2 rounded-full text-sm font-medium transition-colors bg-slate-800 text-white border border-slate-800 hover:bg-slate-700 cursor-pointer" data-filter="all">All</button>
    @foreach($categories as $category)
    <button class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer" data-filter="{{ $category }}">{{ ucfirst($category) }}</button>
    @endforeach
    @foreach($roomTypes as $roomType)
    <button class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer" data-filter="room_type_{{ $roomType->id }}">{{ $roomType->name }}</button>
    @endforeach
</div>

<!-- Gallery Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="gallery-grid">
    @forelse($images as $index => $image)
    @php
        $itemCategory = strtolower($image->category);
        if ($itemCategory === 'rooms' && $image->imageable_type === 'App\\Models\\RoomType') {
            $itemCategory = 'room_type_' . $image->imageable_id;
        }
    @endphp
    <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-sm border border-slate-100 bg-slate-100 aspect-[4/3]" data-category="{{ $itemCategory }}" data-index="{{ $index }}">
        <img src="{{ asset('storage/' . $image->filename) }}" alt="{{ $image->alt_text }}" class="w-full h-full object-cover cursor-pointer transition-transform duration-500 group-hover:scale-105" onclick="openLightbox({{ $index }})">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none flex flex-col justify-end p-4">
            <span class="text-xs font-bold text-primary uppercase tracking-wider mb-1">
                @if(strtolower($image->category) === 'rooms' && $image->imageable_type === 'App\\Models\\RoomType')
                    {{ collect($roomTypes)->firstWhere('id', $image->imageable_id)?->name ?? 'Room' }}
                @else
                    {{ ucfirst($image->category) }}
                @endif
            </span>
            <p class="text-white text-sm font-medium line-clamp-1">{{ $image->caption ?? $image->alt_text }}</p>
        </div>

        <!-- Actions -->
        <div class="absolute top-3 right-3 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button type="button" class="w-8 h-8 bg-white/90 backdrop-blur text-slate-700 rounded-lg flex items-center justify-center hover:text-red-500 hover:bg-white transition-colors shadow-sm" onclick="deleteImage({{ $image->id }})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200 border-dashed">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
            <i class="bi bi-images text-3xl"></i>
        </div>
        <h3 class="text-lg font-serif font-bold text-slate-900 mb-1">No Images Found</h3>
        <p class="text-slate-500">Upload some images to populate your gallery.</p>
    </div>
    @endforelse
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="
        document.getElementById('upload-modal').classList.add('hidden');
        document.getElementById('upload-modal').classList.remove('flex');
    "></div>
    <div class="relative w-full bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 md:p-8 z-10" style="max-width: 70%;">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-serif font-bold text-slate-900 italic">Upload New Image</h2>
            <button onclick="
                document.getElementById('upload-modal').classList.add('hidden');
                document.getElementById('upload-modal').classList.remove('flex');
            " class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <form id="upload-image-form" action="#" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5">
                <!-- Upload Destination -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Upload Destination <span class="text-red-500">*</span></label>
                    <select name="upload_type" id="upload_type" onchange="toggleUploadType()" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        <option value="general">General Website</option>
                        <option value="room_type">Specific Room Type</option>
                    </select>
                </div>

                <!-- Image File Input -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Image Files (Multiple allowed) <span class="text-red-500">*</span></label>
                    <input type="file" name="images[]" accept="image/*" multiple class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div id="general_category_wrapper">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Sub Category</label>
                        <select name="category" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            @foreach($categories as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="room_type_wrapper" style="display: none;">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Room Type</label>
                        <select name="room_type_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" disabled>
                            @foreach($roomTypes as $rt)
                            <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Alt Text</label>
                        <input type="text" name="alt_text" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Caption</label>
                    <input type="text" name="caption" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                        <span class="text-sm font-medium text-slate-700">Featured Image</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" checked>
                        <span class="text-sm font-medium text-slate-700">Active (Visible)</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="
                    document.getElementById('upload-modal').classList.add('hidden');
                    document.getElementById('upload-modal').classList.remove('flex');
                " class="px-6 py-3 rounded-xl font-medium text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-colors shadow-sm">Save Image</button>
            </div>
        </form>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <!-- Controls -->
    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <div id="lightbox-counter" class="text-white font-medium text-sm">1 / 10</div>
        <button onclick="closeLightbox()" class="text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- Image Container -->
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center p-4 md:p-12 overflow-hidden touch-pan-y gap-4 md:gap-8">
        <button onclick="prevImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <i class="bi bi-chevron-left text-xl md:text-2xl"></i>
        </button>

        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">
        
        <button onclick="nextImage(event)" class="shrink-0 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full z-10 focus:outline-none shadow-sm">
            <i class="bi bi-chevron-right text-xl md:text-2xl"></i>
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
    window.toggleUploadType = function() {
        const type = document.getElementById('upload_type').value;
        const generalWrapper = document.getElementById('general_category_wrapper');
        const roomWrapper = document.getElementById('room_type_wrapper');
        const generalSelect = generalWrapper.querySelector('select');
        const roomSelect = roomWrapper.querySelector('select');

        if (type === 'general') {
            generalWrapper.style.display = 'block';
            generalSelect.disabled = false;
            roomWrapper.style.display = 'none';
            roomSelect.disabled = true;
        } else {
            generalWrapper.style.display = 'none';
            generalSelect.disabled = true;
            roomWrapper.style.display = 'block';
            roomSelect.disabled = false;
        }
    };

    // Data
    const images = @json($images -> values());
    const roomTypes = @json(collect($roomTypes)->keyBy('id'));
    let currentFilteredImages = [...images];
    let currentIndex = 0;

    // Elements
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxCategory = document.getElementById('lightbox-category');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const imgContainer = document.getElementById('lightbox-img-container');

    // Filtering Logic
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state classes
            filterBtns.forEach(b => {
                b.classList.remove('bg-slate-800', 'text-white', 'border-slate-800', 'hover:bg-slate-700');
                b.classList.add('bg-white', 'text-slate-600', 'border-slate-200', 'hover:bg-slate-50');
            });
            btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200', 'hover:bg-slate-50');
            btn.classList.add('bg-slate-800', 'text-white', 'border-slate-800', 'hover:bg-slate-700');

            const filter = btn.dataset.filter;

            // Filter DOM items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.5s ease-in-out';
                } else {
                    item.style.display = 'none';
                }
            });

            // Update array for lightbox navigation to only cycle through filtered items
            if (filter === 'all') {
                currentFilteredImages = [...images];
            } else {
                currentFilteredImages = images.filter(img => {
                    let imgCat = (img.category || '').toLowerCase();
                    if (imgCat === 'rooms' && img.imageable_type === 'App\\Models\\RoomType') {
                        imgCat = 'room_type_' + img.imageable_id;
                    }
                    return imgCat === filter.toLowerCase();
                });
            }
        });
    });

    // Lightbox Logic
    window.openLightbox = function(globalIndex) {
        const targetImage = images[globalIndex];
        const filteredIndex = currentFilteredImages.findIndex(img => img.id === targetImage.id);

        if (filteredIndex !== -1) {
            currentIndex = filteredIndex;
            updateLightbox();
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            // Small delay to allow display:flex to apply before changing opacity for transition
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
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
        if (currentFilteredImages.length === 0) return;
        currentIndex = (currentIndex + 1) % currentFilteredImages.length;
        animateSlide('right');
    };

    window.prevImage = function(e) {
        if (e) e.stopPropagation();
        if (currentFilteredImages.length === 0) return;
        currentIndex = (currentIndex - 1 + currentFilteredImages.length) % currentFilteredImages.length;
        animateSlide('left');
    };

    function updateLightbox() {
        if (currentFilteredImages.length === 0) return;
        const img = currentFilteredImages[currentIndex];

        lightboxImg.src = '{{ asset("storage") }}/' + img.filename;
        lightboxImg.alt = img.alt_text || 'Gallery Image';
        lightboxCaption.textContent = img.caption || img.alt_text || '';
        if ((img.category || '').toLowerCase() === 'rooms' && img.imageable_type === 'App\\Models\\RoomType') {
            lightboxCategory.textContent = roomTypes[img.imageable_id] ? roomTypes[img.imageable_id].name : 'Room';
        } else {
            lightboxCategory.textContent = img.category || '';
        }
        lightboxCounter.textContent = `${currentIndex + 1} / ${currentFilteredImages.length}`;
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
        if (lightbox.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });

    // Touch Swipe Logic
    let touchStartX = 0;
    let touchEndX = 0;

    imgContainer.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, {
        passive: true
    });

    imgContainer.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, {
        passive: true
    });

    function handleSwipe() {
        const threshold = 50;
        if (touchEndX < touchStartX - threshold) nextImage();
        if (touchEndX > touchStartX + threshold) prevImage();
    }

    // API Interactions
    document.getElementById('upload-image-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.innerText = 'Uploading...';
        submitBtn.disabled = true;

        const files = this.querySelector('input[name="images[]"]').files;
        const uploadType = document.getElementById('upload_type').value;
        const category = this.querySelector('select[name="category"]').value;
        const roomTypeId = this.querySelector('select[name="room_type_id"]').value;
        const altText = this.querySelector('input[name="alt_text"]').value;
        const caption = this.querySelector('input[name="caption"]').value;
        const isActive = this.querySelector('input[name="is_active"]').checked ? 1 : 0;
        const isFeatured = this.querySelector('input[name="is_featured"]').checked ? 1 : 0;

        try {
            // Upload each selected image individually to satisfy the backend's expected single 'image' field
            for (let i = 0; i < files.length; i++) {
                const formData = new FormData();
                formData.append('image', files[i]);
                formData.append('alt_text', altText);
                formData.append('caption', caption);
                formData.append('is_active', isActive);
                formData.append('is_featured', isFeatured);

                if (uploadType === 'general') {
                    formData.append('category', category);
                } else {
                    formData.append('category', 'rooms');
                    formData.append('imageable_type', 'App\\Models\\RoomType');
                    formData.append('imageable_id', roomTypeId);
                }

                const response = await fetch('/api/gallery-images', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                if (!response.ok) {
                    const error = await response.json();
                    let errorMsg = error.message || 'Unknown error';
                    if (error.errors) {
                        // Extract all validation error messages to display them clearly
                        errorMsg = Object.values(error.errors).flat().join('\n');
                    }
                    throw new Error(errorMsg);
                }
            }
            
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            adminToast('Error uploading image(s):\n' + error.message);
            
            // Reset button state on failure
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
        }
    });

    window.deleteImage = async function(id) {
        if (!await adminConfirm('Are you sure you want to delete this image?', { confirmLabel: 'Delete', type: 'danger' })) return;

        try {
            const response = await fetch(`/api/gallery-images/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                adminToast('Error deleting image: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred.');
        }
    };
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush