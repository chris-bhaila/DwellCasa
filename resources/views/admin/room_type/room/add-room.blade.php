@extends('layouts.admin')

@section('title', 'Add Room - DwellCasa Admin')
@section('header_title', 'Add Room')

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
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Add New Room</h1>
        <p class="text-slate-500 mt-1">Add an individual room to your property's inventory.</p>
        </div>
    </div>
</div>

<form id="add-room-form" action="#" method="POST">
    @csrf

    <div class="">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Room Details</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Room Number <span class="text-red-500">*</span></label>
                    <input type="number" name="room_number" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 101, 205B, Penthouse" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Room Name <span class="text-red-500">*</span></label>
                    <input type="text" name="room_name" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. Bagmati, Karnali" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                    <select name="room_type_id" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        <option value="">Select a room type...</option>
                        @foreach($roomTypes ?? [] as $roomType)
                        @if ($roomType->is_standalone == 0 || $roomType->rooms_count == 0)
                        <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Floor</label>
                        <input type="text" name="floor" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 1st Floor, Ground Floor">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Initial Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            <option value="available">Available</option>
                            <option value="maintenance">Under Maintenance</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Any specific notes about this room..."></textarea>
                </div>

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
                <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-4">
                    <a href="{{ route('admin.room_type.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
                    <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Add Room
                    </button>
                </div>
            </div>
        </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('add-room-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Capture all selected checkbox values, or send an empty array if none are selected
        data.amenities = formData.getAll('amenities[]');
        delete data['amenities[]']; // Clean up the array notation key

        try {
            const response = await fetch('/api/rooms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.room_type.index') }}#inventory";
            } else {
                const errorData = await response.json();
                let errorMessage = 'Error adding room: ' + (errorData.message || 'Unknown error');
                if (errorData.errors) {
                    errorMessage += '\n' + Object.values(errorData.errors).flat().join('\n');
                }
                adminToast(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while adding the room.');
        }
    });
</script>
@endpush