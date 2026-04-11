@extends('layouts.admin')

@section('title', 'Edit Room Type - DwellCasa Admin')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.room_type.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Edit Room Type</h1>
            <p class="text-slate-500 mt-1">Update details, pricing, and availability for this room category.</p>
        </div>
    </div>
    <div>
        <a href="{{ url('/rooms') }}" target="_blank" class="inline-flex items-center text-sm font-medium text-[#A89070] hover:text-[#8E795E] transition-colors">
            View on Website
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </a>
    </div>
</div>

<form id="edit-room-type-form" action="#" method="POST">
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
                        <input type="text" name="name" value="{{ $roomType->name }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" rows="6" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">{{ $roomType->description }}</textarea>
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
                        <input type="number" name="price_per_night" value="{{ round($roomType->price_per_night) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Price Per Month (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_month" value="{{ round($roomType->price_per_month) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Max Occupancy <span class="text-red-500">*</span></label>
                        <input type="number" name="max_occupancy" value="{{ $roomType->max_occupancy }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Room Size (Sq. Ft.)</label>
                        <input type="text" name="size_sqft" value="{{ $roomType->size_sqft }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 450">
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
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" {{ $roomType->amenities->contains($amenity->id) ? 'checked' : '' }}>
                            </label>
                        @endforeach
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
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" {{ ($roomType->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700">Active (Visible on website)</label>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                            Save Changes
                        </button>
                        <button type="button" class="w-full bg-red-50 text-red-600 px-6 py-3 rounded-xl font-medium hover:bg-red-100 transition-all shadow-sm">
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
                        <img src="{{ $roomType->image ?? 'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=800' }}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Image URL</label>
                        <input type="url" name="image" value="{{ $roomType->image ?? '' }}" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="https://...">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('edit-room-type-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data.is_active = this.querySelector('#is_active').checked ? 1 : 0;
        data.amenities = formData.getAll('amenities[]');
        
        try {
            const response = await fetch(`/api/room-types/${data.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                alert('Error updating room type: ' + (error.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred updating the room type.');
        }
    });
</script>
@endpush
