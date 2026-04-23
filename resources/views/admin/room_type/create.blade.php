@extends('layouts.admin')

@section('title', 'Add Room Type - DwellCasa Admin')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.room_type.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Add New Room Type</h1>
            <p class="text-slate-500 mt-1">Create a new room category for your property.</p>
        </div>
    </div>
</div>

<form id="add-room-type-form" action="#" method="POST" enctype="multipart/form-data">
    @csrf

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
                        <input type="text" name="name" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" rows="6" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors"></textarea>
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
                        <input type="number" name="price_per_night" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Price Per Month (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_month" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Max Occupancy <span class="text-red-500">*</span></label>
                        <input type="number" name="max_occupancy" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Room Size (Sq. Ft.)</label>
                        <input type="text" name="size_sqft" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 450">
                    </div>
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
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Gallery Images -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Gallery Images</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload Multiple Images</label>
                        <input type="file" name="images[]" accept="image/*" multiple class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>
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
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" checked>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700">Active (Visible on website)</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_standalone" value="0">
                        <input type="checkbox" name="is_standalone" id="is_standalone" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                        <label for="is_standalone" class="ml-3 text-sm font-medium text-slate-700">Standalone Property (Only select if there is only one of this room type)</label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                            Create Room Type
                        </button>
                        <a href="{{ route('admin.room_type.index') }}" class="w-full text-center bg-slate-50 text-slate-600 px-6 py-3 rounded-xl font-medium hover:bg-slate-100 transition-all shadow-sm">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Featured Image</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Thumbnail</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('add-room-type-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('is_active', this.querySelector('#is_active').checked ? 1 : 0);
        formData.set('is_standalone', this.querySelector('#is_standalone').checked ? 1 : 0);
        
        try {
            const response = await fetch('/api/room-types', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.room_type.index') }}";
            } else {
                const error = await response.json();
                alert('Error creating room type: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred creating the room type.');
        }
    });
</script>
@endpush
