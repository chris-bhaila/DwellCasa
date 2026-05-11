@extends('layouts.admin')

@section('title', 'Edit Room Type - DwellCasa Admin')
@section('header_title', 'Edit Room Type')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.room_type.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Edit Room Type</h1>
        <p class="text-slate-500 mt-1">Update details, pricing, and availability for this room category.</p>
        </div>
    </div>
    <div>
        <a href="{{ url('/rooms') }}" target="_blank" class="inline-flex items-center text-sm font-medium text-[#A89070] hover:text-[#8E795E] transition-colors">
            View on Website
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        </a>
    </div>
</div>

<form id="edit-room-type-form" action="#" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{ $roomType->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Basic Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Room Type Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $roomType->name }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" rows="6" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">{{ $roomType->description }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing & Capacity -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Pricing & Capacity</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Price Per Night (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_night" value="{{ round($roomType->price_per_night) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Price Per Month (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_month" value="{{ round($roomType->price_per_month) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Max Occupancy <span class="text-red-500">*</span></label>
                        <input type="number" name="max_occupancy" value="{{ $roomType->max_occupancy }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Room Size (Sq. Ft.)</label>
                        <input type="text" name="size_sqft" value="{{ $roomType->size_sqft }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 450">
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Total Rooms <span class="text-red-500">*</span></label>
                        <input type="number" name="rooms_count" value="{{ $roomType->rooms_count }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div> -->
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Amenities</h2>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-slate-700 mb-4">Select Room Amenities</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-2">
                        @foreach($amenities ?? [] as $amenity)
                        <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <span class="text-xl text-black flex items-center gap-3">{!! $amenity->icon ?: '✨' !!}
                                <span class="text-sm font-medium text-slate-700">{{ $amenity->name }}</span></span>
                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded cursor-pointer text-primary focus:ring-primary w-5 h-5 border-slate-300" {{ $roomType->amenities->contains($amenity->id) ? 'checked' : '' }}>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Room Images -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Room Images</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload More Images</label>
                        <input type="file" name="images[]" accept="image/*" multiple class="w-full cursor-pointer rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    @if($roomType->galleryImages && $roomType->galleryImages->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                            @foreach($roomType->galleryImages as $index => $image)
                                <div class="rounded-xl overflow-hidden shadow-sm border border-slate-100 aspect-[4/3] relative group">
                                    <img src="{{ asset('storage/' . $image->filename) }}" alt="{{ $image->alt_text }}" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" onclick="openLightbox({{ $index }})">
                                    <button type="button" onclick="deleteGalleryImage({{ $image->id }})" class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur text-slate-700 rounded-lg flex items-center justify-center hover:text-red-500 hover:bg-white transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="w-full bg-slate-50 py-8 rounded-xl flex items-center justify-center border border-slate-200 border-dashed mt-4">
                            <span class="text-slate-500 text-sm">No gallery images uploaded for this room type.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar (Images & Status) -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Status & Publish -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Publishing</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded text-primary cursor-pointer focus:ring-primary w-5 h-5 border-slate-300" {{ ($roomType->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700">Active (Visible on website)</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_standalone" value="0">
                        <input type="checkbox" name="is_standalone" id="is_standalone" value="1" class="rounded cursor-pointer text-primary focus:ring-primary w-5 h-5 border-slate-300" {{ ($roomType->is_standalone ?? false) ? 'checked' : '' }}>
                        <label for="is_standalone" class="ml-3 text-sm font-medium text-slate-700">Standalone Property (Only select if there is only one of this room type)</label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-primary text-white px-6 py-3 cursor-pointer rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                            Save Changes
                        </button>
                        <button type="button" class="w-full bg-red-50 text-red-600 px-6 cursor-pointer py-3 rounded-xl font-medium hover:bg-red-100 transition-all shadow-sm">
                            Delete Room Type
                        </button>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Featured Image</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="aspect-video w-full bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                    <img src="{{ $roomType->thumbnail ? asset('storage/' . $roomType->thumbnail) : ($roomType->image ?? 'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=800') }}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full rounded-xl cursor-pointer border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-black/95 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <!-- Controls -->
    <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
        <div id="lightbox-counter" class="text-white font-medium text-sm">1 / 10</div>
        <button type="button" onclick="closeLightbox()" class="text-black cursor-pointer bg-white/80 hover:bg-white transition-colors w-10 h-10 flex items-center justify-center rounded-full">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <button type="button" onclick="prevImage(event)" class="absolute cursor-pointer left-4 top-1/2 -translate-y-1/2 z-20 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full focus:outline-none shadow-sm">
        <i class="bi bi-chevron-left text-xl md:text-2xl"></i>
    </button>

    <!-- Image Container -->
    <div id="lightbox-img-container" class="relative w-full h-full flex items-center justify-center px-16 md:px-20 py-4 overflow-hidden touch-pan-y">
        <img id="lightbox-img" src="" alt="" class="max-h-full min-w-0 object-contain select-none transition-transform duration-300 shadow-2xl">
    </div>

    <button type="button" onclick="nextImage(event)" class="absolute cursor-pointer right-4 top-1/2 -translate-y-1/2 z-20 text-black bg-white/80 hover:bg-white transition-colors w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full focus:outline-none shadow-sm">
        <i class="bi bi-chevron-right text-xl md:text-2xl"></i>
    </button>

    <!-- Caption -->
    <div class="absolute bottom-0 left-0 w-full p-6 text-center z-10 bg-gradient-to-t from-black/80 to-transparent">
        <h3 id="lightbox-caption" class="text-white text-lg font-serif italic mb-1"></h3>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('edit-room-type-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.set('is_active', this.querySelector('#is_active').checked ? 1 : 0);
        formData.set('is_standalone', this.querySelector('#is_standalone').checked ? 1 : 0);

        try {
            const response = await fetch(`/api/room-types/${formData.get('id')}`, {
                method: 'POST', // Standard Form data interprets the hidden _method field automatically
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                adminToast('Error updating room type: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred updating the room type.');
        }
    });

    window.deleteGalleryImage = async function(imageId) {
        if (!await adminConfirm('Are you sure you want to delete this image?', { confirmLabel: 'Delete', type: 'danger' })) return;

        try {
            const response = await fetch(`/room-types/{{ $roomType->id }}/images/${imageId}`, {
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
            adminToast('An error occurred deleting the image.');
        }
    };

    // Lightbox Logic
    const galleryImages = @json($roomType->galleryImages ?? []);
    let currentLightboxIndex = 0;
    
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxCounter = document.getElementById('lightbox-counter');

    window.openLightbox = function(index) {
        currentLightboxIndex = index;
        updateLightbox();
        if (lightbox) {
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeLightbox = function() {
        if (lightbox) {
            lightbox.classList.add('opacity-0');
            setTimeout(() => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        }
    };

    window.nextImage = function(e) {
        if (e) e.stopPropagation();
        if (!galleryImages || galleryImages.length === 0) return;
        currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
        animateSlide('right');
    };

    window.prevImage = function(e) {
        if (e) e.stopPropagation();
        if (!galleryImages || galleryImages.length === 0) return;
        currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
        animateSlide('left');
    };

    function updateLightbox() {
        if (!galleryImages || galleryImages.length === 0) return;
        const img = galleryImages[currentLightboxIndex];

        if (lightboxImg) {
            lightboxImg.src = '{{ asset("storage") }}/' + img.filename;
            lightboxImg.alt = img.alt_text || 'Gallery Image';
        }
        if (lightboxCaption) {
            lightboxCaption.textContent = img.caption || img.alt_text || '';
        }
        if (lightboxCounter) {
            lightboxCounter.textContent = `${currentLightboxIndex + 1} / ${galleryImages.length}`;
        }
    }

    function animateSlide(direction) {
        if (!lightboxImg) return;
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

    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endpush