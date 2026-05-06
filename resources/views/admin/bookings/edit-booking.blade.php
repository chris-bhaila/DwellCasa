@extends('layouts.admin')

@section('title', 'Edit Booking - DwellCasa Admin')
@section('header_title', 'Edit Booking')

@section('content')

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Edit Booking</h1>
        <p class="text-slate-500 mt-1">Update details for booking #{{ $booking->booking_ref }}.</p>
        </div>
    </div>
</div>

<form id="edit-booking-form" action="#" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{ $booking->id }}">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Form Section -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Booking Details</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Guest Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Name <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_name" value="{{ old('guest_name', $booking->guest->full_name ?? $booking->guest_name) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Email <span class="text-red-500">*</span></label>
                                <input type="email" name="guest_email" value="{{ old('guest_email', $booking->guest->email ?? $booking->guest_email) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guest Phone Number</label>
                                <input type="text" name="guest_phone" value="{{ old('guest_phone', $booking->guest->phone ?? $booking->guest_phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Stay Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Stay Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Check In <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" value="{{ old('check_in_date', \Carbon\Carbon::parse($booking->check_in_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Check Out <span class="text-red-500">*</span></label>
                                <input type="date" name="check_out_date" value="{{ old('check_out_date', \Carbon\Carbon::parse($booking->check_out_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Room Type <span class="text-red-500">*</span></label>
                                <select name="room_type_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                                    <option value="">Select a room...</option>
                                    @foreach($roomTypes ?? [] as $room)
                                    <option value="{{ $room->id }}" data-price-night="{{ $room->price_per_night }}" data-price-month="{{ $room->price_per_month }}" {{ old('room_type_id', $booking->room_type_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Guests</label>
                                <input type="number" name="num_guests" min="1" value="{{ old('num_guests', $booking->num_guests) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Special Requests</label>
                                <textarea name="special_requests" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. extra pillows, late check-in...">{{ old('special_requests', $booking->special_requests) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Details -->
                    <div class="p-6 rounded-xl border border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-serif font-semibold text-slate-800 italic mb-4">Financials</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Stay Type</label>
                                <select name="stay_type" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                                    <option value="short_term" {{ old('stay_type', $booking->stay_type) == 'short_term' ? 'selected' : '' }}>Short Term</option>
                                    <option value="long_term" {{ old('stay_type', $booking->stay_type) == 'long_term' ? 'selected' : '' }}>Long Term</option>
                                </select>
                            </div>
                            <div id="rate-per-night-wrapper">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Night</label>
                                <input type="number" name="rate_per_night" value="{{ old('rate_per_night', !is_null($booking->rate_per_night) ? round($booking->rate_per_night) : '') }}" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div id="rate-per-month-wrapper">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Rate Per Month</label>
                                <input type="number" name="rate_per_month" value="{{ old('rate_per_month', !is_null($booking->rate_per_month) ? round($booking->rate_per_month) : '') }}" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Total Amount <span class="text-red-500">*</span></label>
                                <input type="number" name="total_amount" value="{{ old('total_amount', !is_null($booking->total_amount) ? round($booking->total_amount) : '') }}" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Discount</label>
                                <input type="number" name="discount" value="{{ old('discount', !is_null($booking->discount) ? round($booking->discount) : '') }}" step="1" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Deposit Amount</label>
                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', !is_null($booking->deposit_amount) ? round($booking->deposit_amount) : '') }}" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Amount Paid</label>
                                <input type="number" name="amount_paid" value="{{ old('amount_paid', !is_null($booking->amount_paid) ? round($booking->amount_paid) : '') }}" step="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-6 border-t border-slate-100 flex justify-end gap-4 p-6">
                    <a href="{{ route('admin.bookings') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 px-6 py-3">Cancel</a>
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic">Status</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Booking Status</label>
                        <select name="status" form="edit-booking-form" class="w-full border rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="checked_in" {{ old('status', $booking->status) == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="checked_out" {{ old('status', $booking->status) == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div> -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Status</label>
                        <select name="payment_status" form="edit-booking-form" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                            <option value="unpaid" {{ old('payment_status', $booking->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="deposit_paid" {{ old('payment_status', $booking->payment_status) == 'deposit_paid' ? 'selected' : '' }}>Deposit Paid</option>
                            <option value="partially_paid" {{ old('payment_status', $booking->payment_status) == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="fully_paid" {{ old('payment_status', $booking->payment_status) == 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="refunded" {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="pt-6 border-t border-slate-100">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Admin Notes (Internal)</label>
                        <textarea name="admin_notes" form="edit-booking-form" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Internal notes about this booking...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                    </div>
                </div>
            </div>
            {{-- Sidebar action buttons --}}
            @if($booking->status === 'pending')
            <div class="mt-4">
                <button type="button" onclick="confirmBooking()" class="w-full bg-blue-600 text-white px-4 py-3 rounded-xl font-medium hover:bg-blue-700 transition-all shadow-sm">
                    Confirm Booking
                </button>
            </div>
            @endif
            @if(!in_array($booking->status, ['cancelled', 'checked_out']))
            <div class="mt-4">
                <button type="button" onclick="cancelBooking()"
                    class="w-full bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-xl font-medium hover:bg-red-100 transition-all">
                    Cancel Booking
                </button>
            </div>
            @endif
            @if($booking->status === 'confirmed')
            <div class="mt-4">
                <button type="button" onclick="openCheckInModal()" class="w-full bg-green-700 text-white px-4 py-3 rounded-xl font-medium hover:bg-green-800 transition-all shadow-sm">
                    Check In Guest
                </button>
            </div>
            @endif

            @if($booking->status === 'checked_in')
            <div class="mt-4">
                <button type="button" onclick="openCheckOutModal()" class="w-full bg-slate-700 text-white px-4 py-3 rounded-xl font-medium hover:bg-slate-800 transition-all shadow-sm">
                    Check Out Guest
                </button>
            </div>
            @endif
        </div>
    </div>
</form>

<!-- Check In Modal -->
<div id="check-in-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Check In Guest</h2>
            <button type="button" onclick="closeCheckInModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="check-in-form" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Booking ID</label>
                <input type="text" value="{{ $booking->booking_ref }}" readonly class="w-full rounded-xl border-slate-200 px-4 py-3 bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Assign Room <span class="text-red-500">*</span></label>
                <select name="room_id" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    <option value="">Select a room...</option>
                    @foreach($rooms ?? [] as $room)
                    <option value="{{ $room->id }}">Room {{ $room->room_number }} ({{ ucfirst($room->status) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Checked In By <span class="text-red-500">*</span></label>
                <select name="checked_in_by" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    <option value="">Select staff...</option>
                    @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ auth()->check() && auth()->id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="early_check_in" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                    <span class="text-sm font-medium text-slate-700">Early Check-in</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="id_verified" value="1" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                    <span class="text-sm font-medium text-slate-700">ID Verified</span>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Append check-in notes...">{{ $booking->admin_notes }}</textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-4">
                <button type="button" onclick="closeCheckInModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-medium bg-primary text-white hover:bg-[#8E795E] transition-colors shadow-sm">Confirm Check In</button>
            </div>
        </form>
    </div>
</div>
<!-- Check Out Modal -->
<div id="check-out-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Check Out Guest</h2>
            <button type="button" onclick="closeCheckOutModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="check-out-form" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Booking ID</label>
                <input type="text" value="{{ $booking->booking_ref }}" readonly class="w-full rounded-xl border-slate-200 px-4 py-3 bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="room_id" value="{{ $booking->room_id }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Checked Out By <span class="text-red-500">*</span></label>
                <select name="checked_out_by" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    <option value="">Select staff...</option>
                    @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ auth()->check() && auth()->id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Room Condition <span class="text-red-500">*</span></label>
                <select name="room_condition" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" required>
                    <option value="good">Good</option>
                    <option value="needs_cleaning">Needs Cleaning</option>
                    <option value="damaged">Damaged</option>
                </select>
            </div>
            <div id="damage-notes-wrapper" class="hidden">
                <label class="block text-sm font-medium text-slate-700 mb-2">Damage Notes <span class="text-red-500">*</span></label>
                <textarea name="damage_notes" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Describe the damage..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Extra Charges (Rs.)</label>
                <input type="number" name="extra_charges" step="1" min="0" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="0">
            </div>
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="late_check_out" value="1" id="late_check_out" class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                <label for="late_check_out" class="text-sm font-medium text-slate-700 cursor-pointer">Late Check-out</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors" placeholder="Any checkout notes..."></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-4">
                <button type="button" onclick="closeCheckOutModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-medium bg-slate-700 text-white hover:bg-slate-800 transition-colors shadow-sm">Confirm Check Out</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('edit-booking-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const bookingId = data.id;

        try {
            const response = await fetch(`/api/bookings/${bookingId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.bookings') }}";
            } else {
                const error = await response.json();
                let errorMessage = 'Error updating booking: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                adminToast(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while updating the booking.');
        }
    });

    function openCheckInModal() {
        const modal = document.getElementById('check-in-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
        }, 10);
    }

    function closeCheckInModal() {
        const modal = document.getElementById('check-in-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    document.getElementById('check-in-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        data.early_check_in = formData.get('early_check_in') ? 1 : 0;
        data.id_verified = formData.get('id_verified') ? 1 : 0;
        data.checked_in_at = new Date().toISOString().slice(0, 19).replace('T', ' '); // YYYY-MM-DD HH:mm:ss format

        try {
            const response = await fetch('/api/check-ins', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.reload(); // Reload immediately to see updated status
            } else {
                const error = await response.json();
                let errorMessage = 'Error during check-in: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                adminToast(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred during check-in.');
        }
    });

    function openCheckOutModal() {
        const modal = document.getElementById('check-out-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
        }, 10);
    }

    function closeCheckOutModal() {
        const modal = document.getElementById('check-out-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Show damage notes when room condition is damaged
    document.querySelector('select[name="room_condition"]')?.addEventListener('change', function() {
        const damageWrapper = document.getElementById('damage-notes-wrapper');
        if (this.value === 'damaged') {
            damageWrapper.classList.remove('hidden');
            damageWrapper.querySelector('textarea').required = true;
        } else {
            damageWrapper.classList.add('hidden');
            damageWrapper.querySelector('textarea').required = false;
        }
    });

    document.getElementById('check-out-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        data.late_check_out = formData.get('late_check_out') ? 1 : 0;
        data.checked_out_at = new Date().toISOString().slice(0, 19).replace('T', ' ');

        try {
            const response = await fetch('/api/check-outs', {
                method: 'POST',
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
                let errorMessage = 'Error during check-out: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                adminToast(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred during check-out.');
        }
    });

    // Confirm booking (pending → confirmed)
    async function confirmBooking() {
        if (!await adminConfirm('Are you sure you want to confirm this booking?', { confirmLabel: 'Confirm Booking', type: 'primary' })) return;

        try {
            const response = await fetch(`/api/bookings/{{ $booking->id }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: 'confirmed'
                })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                adminToast('Error confirming booking.');
            }
        } catch (error) {
            adminToast('An error occurred.');
        }
    }

    async function cancelBooking() {
        if (!await adminConfirm('Are you sure you want to cancel this booking? This cannot be undone.', { confirmLabel: 'Cancel Booking', type: 'danger' })) return;

        try {
            const response = await fetch(`/api/bookings/{{ $booking->id }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: 'cancelled'
                })
            });

            if (response.ok) {
                window.location.href = "{{ route('admin.bookings') }}";
            } else {
                adminToast('Error cancelling booking.');
            }
        } catch (error) {
            adminToast('An error occurred.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const stayTypeSelect = document.querySelector('select[name="stay_type"]');
        const roomTypeSelect = document.querySelector('select[name="room_type_id"]');
        const checkInInput = document.querySelector('input[name="check_in_date"]');
        const checkOutInput = document.querySelector('input[name="check_out_date"]');
        const ratePerNightInput = document.querySelector('input[name="rate_per_night"]');
        const ratePerMonthInput = document.querySelector('input[name="rate_per_month"]');
        const totalAmountInput = document.querySelector('input[name="total_amount"]');

        function calculateTotal() {
            if (!roomTypeSelect.value) return;

            const selectedRoom = roomTypeSelect.options[roomTypeSelect.selectedIndex];
            const priceNight = parseFloat(selectedRoom.dataset.priceNight) || 0;
            const priceMonth = parseFloat(selectedRoom.dataset.priceMonth) || 0;

            if (stayTypeSelect.value === 'short_term') {
                ratePerNightInput.value = Math.round(priceNight);
                const checkInDate = new Date(checkInInput.value);
                const checkOutDate = new Date(checkOutInput.value);
                if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
                    const diffDays = Math.ceil(Math.abs(checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                    totalAmountInput.value = Math.round(priceNight * diffDays);
                }
            } else {
                ratePerMonthInput.value = Math.round(priceMonth);
                totalAmountInput.value = Math.round(priceMonth);
            }
        }

        if (roomTypeSelect) {
            roomTypeSelect.addEventListener('change', calculateTotal);
            checkInInput.addEventListener('change', calculateTotal);
            checkOutInput.addEventListener('change', calculateTotal);
            stayTypeSelect.addEventListener('change', calculateTotal);
        }
    });
</script>
@endpush