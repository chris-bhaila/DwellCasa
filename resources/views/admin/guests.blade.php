@extends('layouts.admin')

@section('title', 'Guests - DwellCasa Admin')
@section('header_title', 'Guests')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Guest Management</h1>
        <p class="text-slate-500 mt-1">View guest profiles, booking history, and manage records.</p>
    </div>
</div>

<!-- Tabs -->
<div class="mb-6 border-b border-slate-200">
    <nav class="flex space-x-6" aria-label="Tabs">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
            class="{{ (!request('filter') || request('filter') === 'all') ? 'border-[#A89070] text-[#A89070]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
            All Guests
        </a>
        @can('edit bookings')
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'trashed']) }}"
            class="{{ request('filter') === 'trashed' ? 'border-red-400 text-red-500' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-1.5">
            <i class="bi bi-trash3"></i> Trash
        </a>
        @endcan
    </nav>
</div>

@if($filter === 'trashed')
<div class="mb-4 flex items-center gap-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
    <i class="bi bi-info-circle"></i>
    Deleted guests are kept for 90 days before being permanently removed.
</div>
@endif

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Guest</th>
                    <th class="p-4 font-medium">Phone</th>
                    <th class="p-4 font-medium">Bookings</th>
                    <th class="p-4 font-medium">Total Stays</th>
                    <th class="p-4 font-medium">Last Booking</th>
                    @if($filter === 'trashed')
                    <th class="p-4 font-medium">Deleted</th>
                    @endif
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($guests as $guest)
                @php
                    $completedBookings = $guest->bookings->whereIn('status', ['checked_out']);
                    $totalNights = $completedBookings->sum(fn($b) =>
                        $b->check_in_date && $b->check_out_date
                            ? \Carbon\Carbon::parse($b->check_in_date)->diffInDays($b->check_out_date)
                            : 0
                    );
                    $lastBooking = $guest->bookings->sortByDesc('created_at')->first();
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors {{ $filter === 'trashed' ? 'opacity-75' : '' }}">
                    <td class="p-4">
                        <div class="font-bold text-slate-900">{{ $guest->full_name }}</div>
                        <div class="text-slate-500 text-sm mt-0.5">{{ $guest->email }}</div>
                    </td>
                    <td class="p-4 text-slate-600">
                        {{ $guest->phone ?? '—' }}
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-100 text-slate-700">
                            {{ $guest->bookings_count }} {{ Str::plural('booking', $guest->bookings_count) }}
                        </span>
                    </td>
                    <td class="p-4 text-slate-700">
                        @if($totalNights > 0)
                            {{ $totalNights }} {{ Str::plural('night', $totalNights) }}
                        @else
                            <span class="text-slate-400 text-sm italic">No completed stays</span>
                        @endif
                    </td>
                    <td class="p-4 text-slate-600 text-xs">
                        @if($lastBooking)
                            {{ \Carbon\Carbon::parse($lastBooking->created_at)->format('M d, Y') }}
                            <div class="text-slate-400 mt-0.5">{{ $lastBooking->booking_ref }}</div>
                        @else
                            <span class="text-slate-400 italic">—</span>
                        @endif
                    </td>
                    @if($filter === 'trashed')
                    <td class="p-4 text-slate-500 text-sm">
                        {{ $guest->deleted_at ? $guest->deleted_at->format('M d, Y') : '—' }}
                    </td>
                    @endif
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($filter === 'trashed')
                            @can('edit bookings')
                            <button onclick="restoreGuest({{ $guest->id }}, '{{ addslashes($guest->full_name) }}')"
                                class="w-8 h-8 flex items-center justify-center text-green-500 hover:text-green-700 transition-colors rounded-md hover:bg-green-50"
                                title="Restore">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button onclick="forceDeleteGuest({{ $guest->id }}, '{{ addslashes($guest->full_name) }}')"
                                class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 transition-colors rounded-md hover:bg-red-50"
                                title="Delete permanently">
                                <i class="bi bi-trash3"></i>
                            </button>
                            @endcan
                            @else
                            <button onclick="openGuestModal({{ $guest->id }})"
                                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors rounded-md hover:bg-slate-100"
                                title="View bookings">
                                <i class="bi bi-eye"></i>
                            </button>
                            @can('edit bookings')
                            <button onclick="deleteGuest({{ $guest->id }}, '{{ addslashes($guest->full_name) }}')"
                                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors rounded-md hover:bg-red-50"
                                title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-500">
                        {{ $filter === 'trashed' ? 'No deleted guests.' : 'No guests found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Guest Detail Modal -->
<div id="guest-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-5xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[92vh]">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic" id="modal-guest-name">Guest Profile</h2>
                <p class="text-slate-500 text-sm mt-0.5" id="modal-guest-email"></p>
                <p class="text-slate-500 text-sm" id="modal-guest-phone"></p>
            </div>
            <button type="button" onclick="closeGuestModal()" class="text-slate-400 hover:text-slate-600 transition-colors mt-1">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Stats Row -->
        <div class="px-6 py-4 border-b border-slate-100 flex gap-6 flex-shrink-0" id="modal-stats">
            <div class="text-center">
                <div class="text-2xl font-bold text-slate-900" id="modal-stat-bookings">—</div>
                <div class="text-xs text-slate-500 mt-0.5">Total Bookings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-slate-900" id="modal-stat-nights">—</div>
                <div class="text-xs text-slate-500 mt-0.5">Nights Stayed</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-slate-900" id="modal-stat-spent">—</div>
                <div class="text-xs text-slate-500 mt-0.5">Total Spent</div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="overflow-y-auto flex-1">
            <div class="px-6 pt-5 pb-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Booking History</h3>
            </div>
            <div id="modal-bookings-wrapper">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs border-b border-slate-100">
                            <th class="px-6 py-3 font-medium">Reference</th>
                            <th class="px-6 py-3 font-medium">Room Type</th>
                            <th class="px-6 py-3 font-medium">Check In</th>
                            <th class="px-6 py-3 font-medium">Check Out</th>
                            <th class="px-6 py-3 font-medium">Stay</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium text-right">Amount</th>
                            <th class="px-6 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody id="modal-bookings-body" class="divide-y divide-slate-100">
                    </tbody>
                </table>
                <p id="modal-no-bookings" class="hidden text-center text-slate-500 py-8">No bookings found for this guest.</p>
            </div>
        </div>

        <div class="p-6 border-t border-slate-100 flex justify-end bg-slate-50/50 flex-shrink-0">
            <button type="button" onclick="closeGuestModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const guestData = @json($guestJson);

const statusBadge = {
    pending:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>',
    confirmed:  '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">Confirmed</span>',
    checked_in: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Checked In</span>',
    checked_out:'<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Completed</span>',
    cancelled:  '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">Cancelled</span>',
    no_show:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">No Show</span>',
};

window.openGuestModal = function(id) {
    const guest = guestData[id];
    if (!guest) return;

    document.getElementById('modal-guest-name').textContent = guest.full_name;
    document.getElementById('modal-guest-email').textContent = guest.email || '';
    document.getElementById('modal-guest-phone').textContent = guest.phone ? '📞 ' + guest.phone : '';

    const bookings = guest.bookings;
    const completed = bookings.filter(b => b.status === 'checked_out');
    const totalNights = completed.reduce((s, b) => s + (b.nights || 0), 0);
    const totalSpent = completed.reduce((s, b) => s + parseFloat(b.total_amount || 0), 0);

    document.getElementById('modal-stat-bookings').textContent = bookings.length;
    document.getElementById('modal-stat-nights').textContent = totalNights;
    document.getElementById('modal-stat-spent').textContent = totalSpent > 0 ? 'Rs. ' + totalSpent.toLocaleString('en-IN', {maximumFractionDigits: 0}) : '—';

    const tbody = document.getElementById('modal-bookings-body');
    const noBookings = document.getElementById('modal-no-bookings');

    if (bookings.length === 0) {
        tbody.innerHTML = '';
        noBookings.classList.remove('hidden');
    } else {
        noBookings.classList.add('hidden');
        tbody.innerHTML = bookings.map(b => `
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-3 font-medium text-slate-900">${b.booking_ref}</td>
                <td class="px-6 py-3 text-slate-700">${b.room_type_name}</td>
                <td class="px-6 py-3 text-slate-600">${b.check_in_date || '—'}</td>
                <td class="px-6 py-3 text-slate-600">${b.check_out_date || '—'}</td>
                <td class="px-6 py-3 text-slate-600">${b.nights > 0 ? b.nights + 'n' : '—'}</td>
                <td class="px-6 py-3">${statusBadge[b.status] || b.status}</td>
                <td class="px-6 py-3 text-right text-slate-700 font-medium">${
                    b.amount_paid !== null && b.amount_paid !== undefined
                        ? '<span class="font-semibold">Rs. ' + parseFloat(b.amount_paid).toLocaleString('en-IN', {maximumFractionDigits: 0}) + '</span><span class="text-slate-400 font-normal"> / ' + parseFloat(b.total_amount).toLocaleString('en-IN', {maximumFractionDigits: 0}) + '</span>'
                        : (b.total_amount ? 'Rs. ' + parseFloat(b.total_amount).toLocaleString('en-IN', {maximumFractionDigits: 0}) : '—')
                }</td>
                <td class="px-6 py-3 text-right">
                    <a href="/admin/bookings/${b.id}/edit"
                        class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-[#A89070] transition-colors rounded-md hover:bg-slate-100"
                        title="View booking">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </td>
            </tr>
        `).join('');
    }

    const modal = document.getElementById('guest-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
};

window.closeGuestModal = function() {
    const modal = document.getElementById('guest-modal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
};

window.deleteGuest = async function(id, name) {
    if (!await adminConfirm(`Delete guest "${name}"? Their booking history will be preserved.`, { confirmLabel: 'Delete', type: 'danger' })) return;
    fetch(`/api/guests/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
        else adminToast(data.message ?? 'Delete failed.');
    }).catch(() => adminToast('Delete failed.'));
};

window.restoreGuest = async function(id, name) {
    if (!await adminConfirm(`Restore guest "${name}"?`, { confirmLabel: 'Restore', type: 'primary' })) return;
    fetch(`/api/guests/${id}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
        else adminToast(data.message ?? 'Restore failed.');
    }).catch(() => adminToast('Restore failed.'));
};

window.forceDeleteGuest = async function(id, name) {
    if (!await adminConfirm(`Permanently delete "${name}"? This cannot be undone.`, { confirmLabel: 'Delete Permanently', type: 'danger' })) return;
    fetch(`/api/guests/${id}/force`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
        else adminToast(data.message ?? 'Delete failed.');
    }).catch(() => adminToast('Delete failed.'));
};

// Close modal on backdrop click
document.getElementById('guest-modal').addEventListener('click', function(e) {
    if (e.target === this) closeGuestModal();
});
</script>
@endpush
