@extends('layouts.admin')

@section('title', 'Inventory - DwellCasa Admin')
@section('header_title', 'Inventory')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Inventory</h1>
        <p class="text-slate-500 mt-1">Overview of supplies, equipment, and recent activity.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.inventory.supplies') }}"
            class="px-4 py-2 rounded-xl text-sm font-medium bg-[#A89070]/10 text-[#A89070] hover:bg-[#A89070]/20 transition-colors">
            <i class="bi bi-droplet mr-1.5"></i>Supplies
        </a>
        <a href="{{ route('admin.inventory.equipment') }}"
            class="px-4 py-2 rounded-xl text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
            <i class="bi bi-tv mr-1.5"></i>Equipment
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

    <!-- Total Supplies -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#A89070]/10 flex items-center justify-center text-[#A89070]">
                <i class="bi bi-archive text-lg"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-slate-100 text-slate-500">Supplies</span>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Total Supply Items</p>
        <p class="text-3xl font-bold text-slate-900">{{ $totalSupplies }}</p>
        <p class="text-xs text-slate-400 mt-1">Tracked supply types</p>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 {{ $lowStockCount > 0 ? 'border-amber-200' : '' }}">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-50' : 'bg-slate-100' }} flex items-center justify-center {{ $lowStockCount > 0 ? 'text-amber-500' : 'text-slate-400' }}">
                <i class="bi bi-exclamation-triangle text-lg"></i>
            </div>
            @if($lowStockCount > 0)
            <span class="text-xs font-bold px-2 py-1 rounded-md bg-amber-50 text-amber-600">Attention</span>
            @else
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-green-50 text-green-600">All good</span>
            @endif
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Low Stock</p>
        <p class="text-3xl font-bold {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $lowStockCount }}</p>
        <p class="text-xs text-slate-400 mt-1">At or below minimum level</p>
    </div>

    <!-- Out of Stock -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 {{ $outOfStockCount > 0 ? 'border-rose-200' : '' }}">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $outOfStockCount > 0 ? 'bg-rose-50' : 'bg-slate-100' }} flex items-center justify-center {{ $outOfStockCount > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                <i class="bi bi-x-circle text-lg"></i>
            </div>
            @if($outOfStockCount > 0)
            <span class="text-xs font-bold px-2 py-1 rounded-md bg-rose-50 text-rose-600">Action needed</span>
            @else
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-green-50 text-green-600">All clear</span>
            @endif
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Out of Stock</p>
        <p class="text-3xl font-bold {{ $outOfStockCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $outOfStockCount }}</p>
        <p class="text-xs text-slate-400 mt-1">Zero quantity on hand</p>
    </div>

    <!-- Total Equipment Units -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                <i class="bi bi-tv text-lg"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-blue-50 text-blue-600">Equipment</span>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Total Equipment Units</p>
        <p class="text-3xl font-bold text-slate-900">{{ $totalEquipment }}</p>
        <p class="text-xs text-slate-400 mt-1">Individual tracked units</p>
    </div>

    <!-- Assigned Units -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-500">
                <i class="bi bi-door-open text-lg"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-teal-50 text-teal-600">In rooms</span>
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Assigned Units</p>
        <p class="text-3xl font-bold text-slate-900">{{ $assignedCount }}</p>
        <p class="text-xs text-slate-400 mt-1">Currently in a room</p>
    </div>

    <!-- Damaged / Under Repair -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 {{ $damagedCount > 0 ? 'border-orange-200' : '' }}">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $damagedCount > 0 ? 'bg-orange-50' : 'bg-slate-100' }} flex items-center justify-center {{ $damagedCount > 0 ? 'text-orange-500' : 'text-slate-400' }}">
                <i class="bi bi-tools text-lg"></i>
            </div>
            @if($damagedCount > 0)
            <span class="text-xs font-bold px-2 py-1 rounded-md bg-orange-50 text-orange-600">Needs attention</span>
            @else
            <span class="text-xs font-medium px-2 py-1 rounded-md bg-green-50 text-green-600">All good</span>
            @endif
        </div>
        <p class="text-slate-500 text-xs font-medium mb-1">Damaged / Under Repair</p>
        <p class="text-3xl font-bold {{ $damagedCount > 0 ? 'text-orange-500' : 'text-slate-900' }}">{{ $damagedCount }}</p>
        <p class="text-xs text-slate-400 mt-1">Damaged or being repaired</p>
    </div>

</div>

<!-- Middle Section: Two side-by-side tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Recent Supplies -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Recent Supplies</h2>
            <a href="{{ route('admin.inventory.supplies') }}" class="text-[#A89070] text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[480px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">On Hand</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($recentSupplies as $item)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-900">{{ $item->name }}</p>
                            <p class="text-xs text-slate-400">{{ $item->category->name ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-700">
                            {{ $item->stock ? number_format($item->stock->quantity_on_hand, 2) : '0.00' }}
                            @if($item->unit) <span class="text-slate-400 text-xs">{{ $item->unit }}</span> @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php $status = $item->stock->status ?? 'out_of_stock'; @endphp
                            @if($status === 'available')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>
                            @elseif($status === 'low_stock')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Low Stock</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Out of Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 italic text-sm">No supply items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            <a href="{{ route('admin.inventory.supplies') }}" class="text-sm text-[#A89070] hover:underline">View all supplies &rarr;</a>
        </div>
    </div>

    <!-- Recent Equipment -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-serif font-bold text-slate-900 italic">Recent Equipment</h2>
            <a href="{{ route('admin.inventory.equipment') }}" class="text-[#A89070] text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[480px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Item</th>
                        <th class="px-5 py-3 font-medium">Room</th>
                        <th class="px-5 py-3 font-medium">Condition</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($recentEquipment as $unit)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-900">{{ $unit->item->name ?? '—' }}</p>
                            @if($unit->serial_number)
                            <p class="text-xs text-slate-400">{{ $unit->serial_number }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 text-sm">
                            {{ $unit->currentRoom->room_number ?? 'Storage' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if(in_array($unit->condition, ['new', 'good']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">{{ ucfirst($unit->condition) }}</span>
                            @elseif($unit->condition === 'fair')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Fair</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">{{ ucfirst(str_replace('_', ' ', $unit->condition)) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($unit->status === 'available')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>
                            @elseif($unit->status === 'assigned')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Assigned</span>
                            @elseif($unit->status === 'maintenance')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Maintenance</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Retired</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 italic text-sm">No equipment units found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            <a href="{{ route('admin.inventory.equipment') }}" class="text-sm text-[#A89070] hover:underline">View all equipment &rarr;</a>
        </div>
    </div>

</div>

<!-- Recent Activity -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h2 class="text-lg font-serif font-bold text-slate-900 italic">Recent Activity</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Action</th>
                    <th class="px-5 py-3 font-medium">Item</th>
                    <th class="px-5 py-3 font-medium">Performed By</th>
                    <th class="px-5 py-3 font-medium">Notes</th>
                    <th class="px-5 py-3 font-medium text-right">Date</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($recentLogs as $log)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-5 py-3.5">
                        @php
                            $actionMap = [
                                'restocked'         => ['bg-green-50 text-green-700 border-green-200',  'Restocked'],
                                'used'              => ['bg-blue-50 text-blue-700 border-blue-200',    'Used'],
                                'assigned'          => ['bg-teal-50 text-teal-700 border-teal-200',    'Assigned'],
                                'returned'          => ['bg-slate-100 text-slate-600 border-slate-200','Returned'],
                                'condition_changed' => ['bg-amber-50 text-amber-700 border-amber-200', 'Condition Changed'],
                                'written_off'       => ['bg-rose-50 text-rose-700 border-rose-200',    'Written Off'],
                            ];
                            [$cls, $label] = $actionMap[$log->action] ?? ['bg-slate-100 text-slate-600 border-slate-200', ucfirst($log->action)];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $cls }}">{{ $label }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $log->item->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-slate-600">{{ $log->performedBy->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs max-w-[180px] truncate">{{ $log->notes ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-slate-400 text-xs text-right whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-slate-400 italic text-sm">No activity recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
