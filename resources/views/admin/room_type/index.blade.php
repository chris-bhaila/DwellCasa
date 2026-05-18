@extends('layouts.admin')

@section('title', 'Room Management - DwellCasa Admin')
@section('header_title', 'Room Types')

@section('content')

@php $filter = $filter ?? 'all'; @endphp

<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-start
            md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">
            Room Management
        </h1>
        <p class="text-slate-500 mt-1">
            Manage your property's room inventory and room categories.
        </p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-8 border-b border-slate-200">
    <nav class="flex space-x-8" aria-label="Tabs">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
            class="{{ $filter === 'all' ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}
                   whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            All
        </a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'trashed']) }}"
            class="{{ $filter === 'trashed' ? 'border-red-400 text-red-500' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300' }}
                   whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                   flex items-center gap-1.5">
            <i class="bi bi-trash3"></i> Trash
        </a>
    </nav>
</div>

@if($filter === 'trashed')
<div class="mb-6 flex items-center gap-2 text-sm text-amber-700
            bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
    <i class="bi bi-info-circle"></i>
    Deleted room types and rooms are kept for 90 days before being permanently removed.
</div>
@endif

{{-- ── SECTION 1: Room Inventory ─────────────────────────────────────── --}}
<div class="mb-12">

    {{-- Section header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start
                sm:items-center gap-3 mb-4">
        <div>
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">
                Room Inventory
            </h2>
            @php
                $totalRooms     = $rooms->count();
                $availableCount = $rooms->where('status', 'available')->count();
                $occupiedCount  = $rooms->where('status', 'occupied')->count();
                $reservedCount  = $rooms->where('status', 'reserved')->count();
                $maintenanceCount = $rooms->whereIn('status', ['maintenance', 'out_of_service'])->count();
                $rtFilter = $rtFilter ?? null;
            @endphp
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $totalRooms }} rooms
                @if($filter !== 'trashed')
                · <span class="text-green-600">{{ $availableCount }} available</span>
                @if($occupiedCount > 0) · <span class="text-blue-600">{{ $occupiedCount }} occupied</span>@endif
                @if($reservedCount > 0) · <span class="text-purple-600">{{ $reservedCount }} reserved</span>@endif
                @if($maintenanceCount > 0) · <span class="text-amber-600">{{ $maintenanceCount }} maintenance</span>@endif
                @endif
            </p>
        </div>
        @if($filter !== 'trashed')
        <div class="flex items-center gap-2">
            <select id="rt-filter"
                onchange="applyRtFilter(this.value)"
                class="text-sm font-medium text-slate-700 bg-white border border-slate-200
                       rounded-xl px-3 py-2.5 shadow-sm focus:outline-none focus:ring-2
                       focus:ring-[#A89070]/40 focus:border-[#A89070] cursor-pointer
                       {{ $rtFilter ? 'border-[#A89070] text-[#A89070]' : '' }}">
                <option value="">All Types</option>
                @foreach($roomTypes as $rt)
                <option value="{{ $rt->id }}" {{ $rtFilter == $rt->id ? 'selected' : '' }}>
                    {{ $rt->name }}
                </option>
                @endforeach
            </select>
            <a href="{{ route('admin.room_type.room.add-room') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white
                       border border-slate-200 text-slate-700 text-sm font-medium
                       rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <i class="bi bi-plus-circle text-slate-500"></i> Add Room
            </a>
        </div>
        @endif
    </div>

    {{-- Inventory table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs
                                border-b border-slate-100 uppercase tracking-wide">
                        <th class="px-5 py-3 font-medium">Room</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Floor</th>
                        @if($filter === 'trashed')
                        <th class="px-5 py-3 font-medium">Deleted</th>
                        @else
                        <th class="px-5 py-3 font-medium">Status</th>
                        @endif
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($rooms as $room)
                    @php
                        $numBg = match($room->status ?? '') {
                            'occupied'                    => 'bg-blue-50 text-blue-700',
                            'reserved'                    => 'bg-purple-50 text-purple-700',
                            'maintenance','out_of_service'=> 'bg-amber-50 text-amber-700',
                            default                       => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors
                               {{ $filter === 'trashed' ? 'opacity-75' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center
                                            justify-center text-xs font-bold
                                            flex-shrink-0 {{ $numBg }}">
                                    {{ $room->room_number }}
                                </div>
                                <span class="font-semibold text-slate-900">
                                    Room {{ $room->room_number }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ $room->roomType->name ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-3 text-slate-500">
                            {{ $room->floor ? $room->floor : '—' }}
                        </td>
                        @if($filter === 'trashed')
                        <td class="px-5 py-3 text-slate-500">
                            {{ $room->deleted_at?->format('M d, Y') ?? '—' }}
                        </td>
                        @else
                        <td class="px-5 py-3">
                            @php
                                $statusConfig = match($room->status) {
                                    'available'     => ['bg-green-50 text-green-700 border-green-200',  'Available'],
                                    'occupied'      => ['bg-blue-50 text-blue-700 border-blue-200',     'Occupied'],
                                    'maintenance'   => ['bg-amber-50 text-amber-700 border-amber-200',  'Maintenance'],
                                    'out_of_service'=> ['bg-orange-50 text-orange-700 border-orange-200','Out of Service'],
                                    'reserved'      => ['bg-purple-50 text-purple-700 border-purple-200','Reserved'],
                                    default         => ['bg-slate-50 text-slate-600 border-slate-200',  ucfirst($room->status)],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1
                                         rounded-full text-xs font-medium border
                                         {{ $statusConfig[0] }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                {{ $statusConfig[1] }}
                            </span>
                        </td>
                        @endif
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($filter === 'trashed')
                                <button onclick="restoreRoom({{ $room->id }}, '{{ addslashes($room->room_number) }}')"
                                    class="w-8 h-8 flex items-center justify-center
                                           text-green-500 hover:text-green-700
                                           rounded-lg hover:bg-green-50 transition-colors"
                                    title="Restore">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button onclick="forceDeleteRoom({{ $room->id }}, '{{ addslashes($room->room_number) }}')"
                                    class="w-8 h-8 flex items-center justify-center
                                           text-red-400 hover:text-red-600
                                           rounded-lg hover:bg-red-50 transition-colors"
                                    title="Delete permanently">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @else
                                <a href="{{ route('admin.room_type.room.edit', $room->id) }}"
                                    class="w-8 h-8 flex items-center justify-center
                                           text-slate-400 hover:text-[#A89070]
                                           rounded-lg hover:bg-slate-100 transition-colors"
                                    title="Edit room">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400 italic text-sm">
                            {{ $filter === 'trashed' ? 'No deleted rooms.' : 'No rooms found.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── SECTION 2: Room Types ─────────────────────────────────────────── --}}
<div>

    {{-- Section header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start
                sm:items-center gap-3 mb-4">
        <div>
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">
                Room Types
            </h2>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $roomTypes->count() }} {{ Str::plural('category', $roomTypes->count()) }} configured
            </p>
        </div>
        @if($filter !== 'trashed')
        <a href="{{ route('admin.room_type.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#A89070]
                   text-white text-sm font-medium rounded-xl
                   hover:bg-[#8E795E] transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i> Add Room Type
        </a>
        @endif
    </div>

    {{-- Room type cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($roomTypes as $roomType)
        @php
            $totalTypeRooms = $roomType->rooms->count();
            $availableTypeRooms = $roomType->rooms->where('status', 'available')->count();
            $occupancyPct = $totalTypeRooms > 0
                ? round((($totalTypeRooms - $availableTypeRooms) / $totalTypeRooms) * 100)
                : 0;
            $barColor = $occupancyPct >= 80 ? '#e11d48'
                : ($occupancyPct >= 50 ? '#d97706' : '#16a34a');
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100
                    overflow-hidden hover:shadow-md transition-shadow
                    {{ $filter === 'trashed' ? 'opacity-80' : '' }}">

            {{-- Thumbnail with overlaid name and price --}}
            <div class="relative h-28 overflow-hidden bg-slate-200">
                <img src="{{ $roomType->thumbnail
                    ? asset('storage/' . $roomType->thumbnail)
                    : 'https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&q=80&w=800' }}"
                    alt="{{ $roomType->name }}"
                    class="w-full h-full object-cover
                           {{ $filter !== 'trashed' ? 'group-hover:scale-105 transition-transform duration-700' : '' }}">
                {{-- Dark overlay for text legibility --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                {{-- Room type name overlaid on image --}}
                <div class="absolute bottom-0 left-0 right-0 px-4 pb-3">
                    <h3 class="text-base font-serif font-bold text-white italic leading-tight">
                        {{ $roomType->name }}
                    </h3>
                </div>
                {{-- Price badge --}}
                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm
                            px-2.5 py-1 rounded-full text-xs font-bold text-slate-800 shadow-sm">
                    Rs. {{ number_format($roomType->price_per_night, 0) }}<span class="font-normal text-slate-500">/night</span>
                </div>
                @if($filter === 'trashed')
                <div class="absolute top-3 left-3 bg-red-500/90 px-2 py-0.5
                            rounded-full text-xs font-medium text-white">
                    Deleted {{ $roomType->deleted_at?->format('M d') }}
                </div>
                @endif
            </div>

            {{-- Stats --}}
            <div class="p-4">
                <div class="flex gap-2 mb-3">
                    <div class="flex-1 flex flex-col items-center py-2 px-1
                                bg-slate-50 rounded-lg">
                        <span class="text-lg font-bold text-slate-900 leading-none">
                            {{ $totalTypeRooms }}
                        </span>
                        <span class="text-[10px] text-slate-400 mt-1 uppercase tracking-wide">
                            Rooms
                        </span>
                    </div>
                    <div class="flex-1 flex flex-col items-center py-2 px-1
                                bg-slate-50 rounded-lg">
                        <span class="text-lg font-bold text-slate-900 leading-none">
                            {{ $roomType->max_occupancy ?? 2 }}
                        </span>
                        <span class="text-[10px] text-slate-400 mt-1 uppercase tracking-wide">
                            Guests
                        </span>
                    </div>
                    <div class="flex-1 flex flex-col items-center py-2 px-1
                                bg-slate-50 rounded-lg">
                        <span class="text-lg font-bold leading-none"
                              style="color: {{ $barColor }}">
                            {{ $availableTypeRooms }}
                        </span>
                        <span class="text-[10px] text-slate-400 mt-1 uppercase tracking-wide">
                            Free
                        </span>
                    </div>
                </div>

                {{-- Occupancy bar --}}
                <div class="flex justify-between text-xs text-slate-400 mb-1">
                    <span>Occupancy</span>
                    <span>{{ $occupancyPct }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mb-3">
                    <div class="h-1.5 rounded-full transition-all"
                         style="width: {{ $occupancyPct }}%; background: {{ $barColor }};"></div>
                </div>

                {{-- Action buttons --}}
                @if($filter === 'trashed')
                <div class="flex gap-2">
                    <button onclick="restoreRoomType({{ $roomType->id }}, '{{ addslashes($roomType->name) }}')"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2
                               text-xs font-medium text-green-600 bg-green-50
                               border border-green-200 rounded-xl hover:bg-green-100
                               transition-colors">
                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                    <button onclick="forceDeleteRoomType({{ $roomType->id }}, '{{ addslashes($roomType->name) }}')"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2
                               text-xs font-medium text-red-500 bg-red-50
                               border border-red-200 rounded-xl hover:bg-red-100
                               transition-colors">
                        <i class="bi bi-trash3"></i> Delete Forever
                    </button>
                </div>
                @else
                <a href="{{ route('admin.room_type.edit', $roomType->id) }}"
                    class="flex items-center justify-center gap-1.5 w-full py-2
                           text-xs font-medium text-slate-600 bg-slate-50
                           border border-slate-200 rounded-xl hover:bg-slate-100
                           transition-colors">
                    <i class="bi bi-pencil-square"></i> Manage Details
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center bg-white rounded-2xl
                    border border-slate-200 border-dashed">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center
                        justify-center mx-auto mb-4 text-slate-400">
                <i class="bi bi-building text-3xl"></i>
            </div>
            @if($filter === 'trashed')
            <h3 class="text-lg font-serif font-bold text-slate-900 mb-1">
                No Deleted Room Types
            </h3>
            <p class="text-slate-500">The trash is empty.</p>
            @else
            <h3 class="text-lg font-serif font-bold text-slate-900 mb-1">
                No Room Types Found
            </h3>
            <p class="text-slate-500 mb-6">
                Get started by creating your first room category.
            </p>
            <a href="{{ route('admin.room_type.create') }}"
                class="inline-flex items-center text-white bg-[#A89070]
                       px-6 py-2.5 rounded-xl font-medium
                       hover:bg-[#8E795E] transition-colors shadow-sm">
                + Add Room Type
            </a>
            @endif
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
function applyRtFilter(value) {
    const url = new URL(window.location.href);
    if (value) {
        url.searchParams.set('rt', value);
    } else {
        url.searchParams.delete('rt');
    }
    window.location.href = url.toString();
}
</script>
@if($filter === 'trashed')
<script>
async function restoreRoomType(id, name) {
    if (!await adminConfirm(`Restore room type "${name}"?`, { confirmLabel: 'Restore', type: 'primary' })) return;

    fetch(`/api/room-types/${id}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { flashToast('Room type restored successfully.', 'success'); window.location.reload(); }
        else adminToast(data.message ?? 'Restore failed.', 'error');
    })
    .catch(() => adminToast('Restore failed.', 'error'));
}

async function forceDeleteRoomType(id, name) {
    if (!await adminConfirm(`Permanently delete room type "${name}"? This cannot be undone.`, { confirmLabel: 'Delete Permanently', type: 'danger' })) return;

    fetch(`/api/room-types/${id}/force`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { flashToast('Room type permanently deleted.', 'warning'); window.location.reload(); }
        else adminToast(data.message ?? 'Delete failed.', 'error');
    })
    .catch(() => adminToast('Delete failed.', 'error'));
}

async function restoreRoom(id, number) {
    if (!await adminConfirm(`Restore room "${number}"?`, { confirmLabel: 'Restore', type: 'primary' })) return;

    fetch(`/api/rooms/${id}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { flashToast('Room restored successfully.', 'success'); window.location.reload(); }
        else adminToast(data.message ?? 'Restore failed.', 'error');
    })
    .catch(() => adminToast('Restore failed.', 'error'));
}

async function forceDeleteRoom(id, number) {
    if (!await adminConfirm(`Permanently delete room "${number}"? This cannot be undone.`, { confirmLabel: 'Delete Permanently', type: 'danger' })) return;

    fetch(`/api/rooms/${id}/force`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { flashToast('Room permanently deleted.', 'warning'); window.location.reload(); }
        else adminToast(data.message ?? 'Delete failed.', 'error');
    })
    .catch(() => adminToast('Delete failed.', 'error'));
}
</script>
@endif
@endpush
