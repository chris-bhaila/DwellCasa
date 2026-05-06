@extends('layouts.admin')

@section('title', 'Website Information - DwellCasa Admin')
@section('header_title', 'Website Information')

@section('content')
@php
    $currentLocationId = session('selected_location_id');
    $otherLocations = \App\Models\Location::where('is_active', true)
        ->when($currentLocationId, fn($q) => $q->where('id', '!=', $currentLocationId))
        ->orderBy('name')
        ->get();
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Website Information</h1>
        <p class="text-slate-500 mt-1">Manage global content, headings, and settings for the public website.</p>
    </div>

    @if($otherLocations->isNotEmpty())
    <div x-data="{ open: false, loading: false }" class="relative self-start md:self-auto">
        <button
            @click="open = !open"
            :disabled="loading"
            class="inline-flex items-center gap-2 border border-slate-200 bg-white text-slate-700 px-4 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition-all shadow-sm text-sm disabled:opacity-60">
            <template x-if="!loading">
                <i class="bi bi-download"></i>
            </template>
            <template x-if="loading">
                <i class="bi bi-hourglass-split animate-spin"></i>
            </template>
            <span x-text="loading ? 'Importing...' : 'Import Data From'"></span>
            <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="open = false"
            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
            x-cloak>
            @foreach($otherLocations as $loc)
            <button
                type="button"
                @click="loading = true; open = false; importFrom({{ $loc->id }}).finally(() => loading = false)"
                class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition-colors">
                <i class="bi bi-geo-alt text-slate-400"></i>
                {{ $loc->name }}
            </button>
            @endforeach
        </div>
    </div>
    @endif
</div>

<form id="website-info-form" action="#" method="POST" class="space-y-8" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Home Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Home Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Main Heading</label>
                    <input type="text" name="front_page_main_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading 1</label>
                    <input type="text" name="front_page_sub_heading_1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading 2</label>
                    <input type="text" name="front_page_sub_heading_2" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">End Section Heading</label>
                    <input type="text" name="front_page_end_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">End Section Sub Heading</label>
                    <input type="text" name="front_page_end_sub_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- About Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">About Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="about_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Description</label>
                    <textarea name="about_sub_description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Main Description</label>
                    <textarea name="about_main_description" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
            </div>
        </div>

        <!-- Gallery Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Gallery Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="gallery_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading</label>
                    <input type="text" name="gallery_sub_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Reviews Section</h2>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading</label>
                    <input type="text" name="reviews_sub_heading" placeholder="Guest Experiences" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="reviews_heading" placeholder="What They Say" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Contact & Policies -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Contact & Policies</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contact Sub Heading</label>
                    <input type="text" name="contact_sub_heading" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                    <input type="text" name="contact_address" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="text" name="contact_phone" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="contact_email" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Check-in Time</label>
                        <input type="time" name="check_in" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Time</label>
                        <input type="time" name="check_out" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>
        </div>

        <!-- Website Images -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Website Images</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Homepage Main Image</label>
                    <img id="homepage_main_image_preview" src="" class="hidden w-full h-32 object-cover rounded-xl mb-3 border border-slate-200">
                    <input type="file" name="homepage_main_image" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Homepage End Image</label>
                    <img id="homepage_end_image_preview" src="" class="hidden w-full h-32 object-cover rounded-xl mb-3 border border-slate-200">
                    <input type="file" name="homepage_end_image" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">About Page Image</label>
                    <img id="about_image_preview" src="" class="hidden w-full h-32 object-cover rounded-xl mb-3 border border-slate-200">
                    <input type="file" name="about_image" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Social & Footer -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Social & Footer</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Facebook Link</label>
                        <input type="url" name="facebook_link" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Instagram Link</label>
                        <input type="url" name="instagram_link" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Footer Description</label>
                    <textarea name="footer_description" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-save"></i> Save Changes
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const imageFields = ['homepage_main_image', 'homepage_end_image', 'about_image'];

function fillForm(data) {
    const form = document.getElementById('website-info-form');

    Object.keys(data).forEach(key => {
        const input = form.elements[key];
        if (input) {
            if (input.type === 'time' && data[key]) {
                input.value = data[key].substring(0, 5);
            } else if (input.type !== 'file') {
                input.value = data[key] || '';
            }
        }

        const preview = document.getElementById(key + '_preview');
        if (preview && data[key]) {
            preview.src = '/storage/' + data[key];
            preview.classList.remove('hidden');
        }
    });
}

async function loadImageIntoInput(storagePath, inputName) {
    try {
        const res = await fetch('/storage/' + storagePath);
        if (!res.ok) return;
        const blob = await res.blob();
        const filename = storagePath.split('/').pop();
        const file = new File([blob], filename, { type: blob.type });
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.querySelector(`input[name="${inputName}"]`);
        if (input) input.files = dt.files;
    } catch (e) {
        console.warn('Could not import image for', inputName, e);
    }
}

window.importFrom = async function(locationId) {
    const response = await fetch(`/api/website-info?location_id=${locationId}`);
    const result = await response.json();

    if (!response.ok || !result.data) {
        adminToast('Could not load data for that location.');
        return;
    }

    fillForm(result.data);

    await Promise.all(
        imageFields
            .filter(field => result.data[field])
            .map(field => loadImageIntoInput(result.data[field], field))
    );
};

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const locationId = '{{ session('selected_location_id') }}';
        const response = await fetch(`/api/website-info?location_id=${locationId}`);
        const result = await response.json();
        if (response.ok && result.data) fillForm(result.data);
    } catch (error) {
        console.error('Error fetching website info:', error);
    }
});

document.getElementById('website-info-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);

    // API requires time formats to be H:i
    if (formData.get('check_in')) formData.set('check_in', formData.get('check_in').substring(0, 5));
    if (formData.get('check_out')) formData.set('check_out', formData.get('check_out').substring(0, 5));

    formData.append('location_id', '{{ session('selected_location_id') }}');

    try {
        const response = await fetch('/api/website-info', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        if (response.ok) {
            adminToast('Website information updated successfully.', 'success');
            window.location.reload();
        } else {
            const error = await response.json();
            let errorMsg = 'Error saving changes: ' + (error.message || 'Unknown error');
            if (error.errors) {
                errorMsg += '\n' + Object.values(error.errors).flat().join('\n');
            }
            adminToast(errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        adminToast('An error occurred while saving the changes.');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endpush
