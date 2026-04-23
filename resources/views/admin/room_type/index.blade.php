@extends('layouts.admin')

@section('title', 'Room Management - DwellCasa Admin')

@section('content')

@php
if (!isset($roomTypes)) {
$roomTypes = collect([
(object)[
'id' => 1,
'name' => 'Executive Suite',
'description' => 'Our most luxurious offering featuring panoramic city views, a separate living area, and premium amenities.',
'price_per_night' => 45000,
'max_occupancy' => 2,
'rooms_count' => 8,
'image' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=800'
],
(object)[
'id' => 2,
'name' => 'Deluxe Room',
'description' => 'Spacious and elegantly appointed rooms perfect for couples or business travelers.',
'price_per_night' => 25000,
'max_occupancy' => 3,
'rooms_count' => 15,
'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=800'
]
]);
}
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Room Management</h1>
        <p class="text-slate-500 mt-1">Manage your property's room categories, pricing, and inventory.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.room_type.create') }}" class="relative inline-flex items-center justify-center bg-primary text-white w-10 h-10 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm group">
            <i class="bi bi-plus-lg text-lg transition-transform group-hover:rotate-90"></i>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">Add Room Type</span>
        </a>
    </div>
</div>

<!-- Room Types Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($roomTypes as $roomType)
    <!-- Room Type Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow group flex flex-col">
        <!-- Image Thumbnail -->
        <a href="{{ route('admin.room_type.edit', $roomType->id) }}" class="block h-48 bg-slate-200 relative overflow-hidden">
            <img src="{{ $roomType->thumbnail ? asset('storage/' . $roomType->thumbnail) : ($roomType->image ?? 'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=800') }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg text-sm font-bold text-slate-900 shadow-sm">
                Rs. {{ number_format($roomType->price_per_night, 0) }}<span class="text-xs text-slate-500 font-normal">/night</span>
            </div>
        </a>

        <!-- Content -->
        <div class="p-6 flex flex-col flex-1">
            <h3 class="text-xl font-serif font-bold text-slate-900 italic mb-2">
                <a href="{{ route('admin.room_type.edit', $roomType->id) }}" class="hover:text-primary transition-colors">
                    {{ $roomType->name }}
                </a>
            </h3>
            <p class="text-sm text-slate-500 line-clamp-2 mb-6 flex-1">{{ $roomType->description ?? 'No description provided.' }}</p>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 gap-4 py-4 border-t border-slate-100 mb-4">
                <div class="text-center">
                    <span class="block text-2xl font-bold text-slate-900">{{ $roomType->rooms->count() ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Rooms</span>
                </div>
                <div class="text-center border-l border-slate-100">
                    <span class="block text-2xl font-bold text-slate-900">{{ $roomType->max_occupancy ?? 2 }}</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Max Guests</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end mt-auto">
                <a href="{{ route('admin.room_type.edit', $roomType->id) }}" class="relative inline-flex items-center justify-center w-10 h-10 bg-primary hover:bg-[#8E795E] text-white rounded-xl transition-colors group shadow-sm">
                    <i class="bi bi-pencil-square text-lg"></i>
                    <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal">Manage Details</span>
                </a>
            </div>
        </div>
    </div>
    @empty
    <!-- Empty State -->
    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200 border-dashed">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
            <i class="bi bi-building text-3xl"></i>
        </div>
        <h3 class="text-lg font-serif font-bold text-slate-900 mb-1">No Room Types Found</h3>
        <p class="text-slate-500 mb-6">Get started by creating your first room category.</p>
        <a href="{{ route('admin.room_type.create') }}" class="inline-flex items-center text-white bg-primary px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-colors shadow-sm">
            + Add Room Type
        </a>
    </div>
    @endforelse
</div>

<!-- Room Inventory Section -->
<div id="inventory" class="mt-16 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-serif font-bold text-slate-900 italic">Room Inventory</h2>
        <p class="text-slate-500 mt-1">Manage individual rooms and their current status.</p>
    </div>
    <a href="{{ route('admin.room_type.room.add-room') }}" class="relative inline-flex items-center justify-center bg-white border border-slate-200 text-slate-700 w-10 h-10 rounded-xl font-medium hover:bg-slate-50 transition-all shadow-sm group">
        <i class="bi bi-plus-circle text-lg text-slate-500 group-hover:text-slate-700 transition-colors"></i>
        <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">Add Room</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Room Number</th>
                    <th class="p-4 font-medium">Room Type</th>
                    <th class="p-4 font-medium">Floor</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($rooms as $room)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4 font-bold text-slate-900">{{ $room->room_number }}</td>
                    <td class="p-4 text-slate-700 font-medium">{{ $room->roomType->name ?? 'N/A' }}</td>
                    <td class="p-4 text-slate-700">{{ $room->floor ?? 'N/A' }}</td>
                    <td class="p-4">
                        @if($room->status === 'available')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>
                        @elseif($room->status === 'occupied')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Occupied</span>
                        @elseif($room->status === 'maintenance')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">Maintenance</span>
                        @elseif($room->status === 'out_of_service')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">Out of Service</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">{{ ucfirst($room->status) }}</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.room_type.room.edit', $room->id) }}" class="relative text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] p-2 rounded-lg transition-colors font-medium group">
                                <i class="bi bi-pencil text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Edit Room</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">No individual rooms found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection