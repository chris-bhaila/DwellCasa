@extends('layouts.admin')

@section('title', 'Edit Room - DwellCasa Admin')
@section('header_title', 'Edit Room')

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
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Edit Room Details</h1>
        <p class="text-slate-500 mt-1">Update details and status for this individual room.</p>
        </div>
    </div>
</div>

<form id="edit-room-form" action="#" method="POST">
    @csrf
    @method('PUT')

    <input type="hidden" name="id" value="{{ $room->id }}">

    <div class="">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Room Details</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Room Number / Name <span class="text-red-500">*</span></label>
                    <input type="text" name="room_number" value="{{ $room->room_number }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 101, 205B, Penthouse" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                    <select name="room_type_id" class="w-full cursor-pointer rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        <option value="">Select a room type...</option>
                        @foreach($roomTypes ?? [] as $roomType)
                        @if ($roomType->is_standalone == 0 || $roomType->rooms_count == 0 || $room->room_type_id == $roomType->id)
                        <option value="{{ $roomType->id }}" {{ $room->room_type_id == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Floor</label>
                        <input type="text" name="floor" value="{{ $room->floor }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 1st Floor, Ground Floor">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Current Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl cursor-pointer border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="reserved" {{ $room->status === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Any specific notes about this room...">{{ $room->notes }}</textarea>
                </div>


            </div>
            <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-4">
                <button type="button" onclick="deleteRoom({{ $room->id }})" class="bg-red-50 cursor-pointer text-red-600 cursor-pointer border border-red-200 px-4 py-2.5 rounded-xl font-medium hover:bg-red-100 transition-all flex items-center gap-2">
                    <i class="bi bi-trash"></i> Delete Room
                </button>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.room_type.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
                    <button type="submit" class="bg-primary cursor-pointer text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    window.deleteRoom = async function(id) {
        if (!await adminConfirm('Are you sure you want to delete this room? This action cannot be undone.', { confirmLabel: 'Delete', type: 'danger' })) return;

        try {
            const response = await fetch(`/api/rooms/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.room_type.index') }}#inventory";
            } else {
                const errorData = await response.json();
                adminToast('Error deleting room: ' + (errorData.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while deleting the room.');
        }
    };

    document.getElementById('edit-room-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`/api/rooms/${data.id}`, {
                method: 'PUT',
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
                let errorMessage = 'Error updating room: ' + (errorData.message || 'Unknown error');
                if (errorData.errors) {
                    errorMessage += '\n' + Object.values(errorData.errors).flat().join('\n');
                }
                adminToast(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while updating the room.');
        }
    });
</script>
@endpush