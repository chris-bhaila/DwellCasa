@extends('layouts.admin')
@section('title', 'View Booking - DwellCasa Admin')
@section('header_title', 'Booking Details')

@section('content')

@php
    $guest       = $booking->guest;
    $checkIn     = $booking->checkIn;
    $checkOut    = $booking->checkOut;
    $nights      = \Carbon\Carbon::parse($booking->check_in_date)
                     ->diffInDays($booking->check_out_date);
    $discount    = $booking->discount ?? 0;
    $extra       = $checkOut->extra_charges ?? 0;
    $net         = ($booking->total_amount ?? 0) - $discount;
    $paid        = $booking->amount_paid ?? 0;
    $deposit     = $booking->deposit_amount ?? 0;
    $outstanding = max(0, ($net + $extra) - $paid - $deposit);
    $refund      = $booking->refund_amount ?? 0;

    $docEditHours  = auth()->user()->hasAnyRole(['admin', 'super_admin']) ? 72 : 24;
    $canEditDocument = $checkIn && $checkIn->checked_in_at
        && $checkIn->checked_in_at->diffInHours(now()) <= $docEditHours;
@endphp

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 hover:text-primary shadow-sm border border-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Booking Details</h1>
            <p class="text-slate-500 mt-1">Viewing booking #{{ $booking->booking_ref }}.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Main Section -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Guest Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Guest Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Full Name</p>
                        <p class="text-slate-800 font-medium">{{ $guest->full_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Email</p>
                        <p class="text-slate-800 font-medium">{{ $guest->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Phone</p>
                        <p class="text-slate-800 font-medium">{{ $guest->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Nationality</p>
                        <p class="text-slate-800 font-medium">{{ $guest->nationality ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">ID Type</p>
                        <p class="text-slate-800 font-medium">{{ $guest->id_type ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">ID Number</p>
                        <p class="text-slate-800 font-medium">{{ $guest->id_number ?? '—' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Address</p>
                        <p class="text-slate-800 font-medium">{{ $guest->address ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Stay Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Booking Reference</p>
                        <span class="font-mono text-sm bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg">{{ $booking->booking_ref }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Room Type</p>
                        <p class="text-slate-800 font-medium">{{ $booking->roomType->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Room Number</p>
                        <p class="text-slate-800 font-medium">{{ $booking->room->room_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Stay Type</p>
                        <p class="text-slate-800 font-medium">{{ $booking->stay_type === 'long_term' ? 'Long Term' : 'Short Term' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Check In</p>
                        <p class="text-slate-800 font-medium">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Check Out</p>
                        <p class="text-slate-800 font-medium">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Nights Stayed</p>
                        <p class="text-slate-800 font-medium">{{ $nights }} night{{ $nights != 1 ? 's' : '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Number of Guests</p>
                        <p class="text-slate-800 font-medium">{{ $booking->num_guests ?? '—' }}</p>
                    </div>
                    @if($booking->special_requests)
                    <div class="md:col-span-2">
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Special Requests</p>
                        <p class="text-slate-800 font-medium">{{ $booking->special_requests }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Financial Summary</h2>
            </div>
            <div class="p-6">
                <dl class="divide-y divide-slate-100">
                    @if($booking->stay_type === 'long_term')
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Rate Per Month</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($booking->rate_per_month ?? 0, 0) }}</dd>
                    </div>
                    @else
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Rate Per Night</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($booking->rate_per_night ?? 0, 0) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Total Amount</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($booking->total_amount ?? 0, 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Discount</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($discount, 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-sm font-semibold text-slate-700">Net Amount</dt>
                        <dd class="text-sm font-semibold text-slate-900">Rs. {{ number_format($net, 0) }}</dd>
                    </div>
                    @if($extra > 0)
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-rose-500">Extra Charges</dt>
                        <dd class="text-sm font-medium text-rose-600">+ Rs. {{ number_format($extra, 0) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Amount Paid</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($paid, 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-sm font-semibold {{ $outstanding > 0 ? 'text-rose-600' : 'text-slate-700' }}">Outstanding</dt>
                        <dd class="text-sm font-semibold {{ $outstanding > 0 ? 'text-rose-600' : 'text-slate-900' }}">Rs. {{ number_format($outstanding, 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Deposit Amount</dt>
                        <dd class="text-sm font-medium text-slate-800">Rs. {{ number_format($booking->deposit_amount ?? 0, 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-3 items-center">
                        <dt class="text-sm text-slate-500">Payment Status</dt>
                        <dd>
                            @if($booking->payment_status === 'fully_paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Fully Paid</span>
                            @elseif($booking->payment_status === 'deposit_paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Deposit Paid</span>
                            @elseif($booking->payment_status === 'partially_paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-orange-50 text-orange-700 border border-orange-200">Partially Paid</span>
                            @elseif($booking->payment_status === 'refunded')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-50 text-slate-600 border border-slate-200">Refunded</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200">Unpaid</span>
                            @endif
                        </dd>
                    </div>
                    @if($refund > 0)
                    <div class="flex justify-between py-3">
                        <dt class="text-sm text-slate-500">Refund Processed</dt>
                        <dd class="text-sm font-medium text-slate-800">
                            Rs. {{ number_format($refund, 0) }}
                            @if($booking->refunded_at)
                            <span class="text-xs text-slate-400 ml-1">
                                on {{ $booking->refunded_at->format('M d, Y') }}
                            </span>
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

    </div>

    <!-- Sidebar -->
    <div class="xl:col-span-1 space-y-4">

        <!-- Booking Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Booking Status</h2>
            </div>
            <div class="p-6 flex flex-col items-center gap-4">
                @if($booking->status === 'confirmed')
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-base font-semibold bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                @elseif($booking->status === 'pending')
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-base font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                @elseif($booking->status === 'checked_in')
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-base font-semibold bg-blue-50 text-blue-700 border border-blue-200">Checked In</span>
                @elseif($booking->status === 'checked_out')
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-base font-semibold bg-slate-100 text-slate-700 border border-slate-200">Completed</span>
                @else
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-base font-semibold bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                @endif
                <p class="text-xs text-slate-400">Created {{ $booking->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Check-in Details -->
        @if($checkIn)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Check-in Details</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Checked In At</p>
                    <p class="text-slate-800 font-medium">{{ $checkIn->checked_in_at ? \Carbon\Carbon::parse($checkIn->checked_in_at)->format('M d, Y H:i') : $checkIn->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Checked In By</p>
                    <p class="text-slate-800 font-medium">{{ $checkIn->checkedInBy->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Room Assigned</p>
                    <p class="text-slate-800 font-medium">{{ $checkIn->room_id ? 'Room ' . ($booking->room->room_number ?? $checkIn->room_id) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Early Check-in</p>
                    @if($checkIn->early_check_in)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Yes</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-50 text-slate-500 border border-slate-200">No</span>
                    @endif
                </div>

                {{-- ID Document --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">ID Document</p>
                        @if($canEditDocument)
                        <button onclick="openVerifyIdModal()"
                            class="text-xs font-medium text-primary hover:underline flex items-center gap-1">
                            <i class="bi bi-pencil text-xs"></i>
                            {{ $guestDocument ? 'Edit' : 'Add' }}
                        </button>
                        @endif
                    </div>
                    @if($guestDocument)
                    <div class="flex gap-3 items-start">
                        @if($guestDocument->photo)
                        <a href="{{ asset('storage/' . $guestDocument->photo) }}" target="_blank">
                            <img src="{{ asset('storage/' . $guestDocument->photo) }}" alt="ID Photo"
                                 class="h-16 w-24 object-cover rounded-lg border border-slate-200 hover:opacity-80 transition-opacity">
                        </a>
                        @endif
                        <div class="space-y-0.5 text-sm">
                            @if($guestDocument->document_type)
                            <p class="text-slate-700 font-medium capitalize">{{ str_replace('_', ' ', $guestDocument->document_type) }}</p>
                            @endif
                            @if($guestDocument->id_number)
                            <p class="text-slate-500">{{ $guestDocument->id_number }}</p>
                            @endif
                            @if($guestDocument->nationality)
                            <p class="text-slate-500">{{ $guestDocument->nationality }}</p>
                            @endif
                            @if($guestDocument->date_of_birth)
                            <p class="text-slate-500">DOB: {{ $guestDocument->date_of_birth->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                        <i class="bi bi-patch-check-fill mr-1"></i> Verified
                    </span>
                    @elseif($checkIn)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-600 border border-red-200">Not Verified</span>
                    @endif
                </div>
                @if($checkIn->notes)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Notes</p>
                    <p class="text-slate-700 text-sm">{{ $checkIn->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Check-out Details -->
        @if($checkOut)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Check-out Details</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Checked Out At</p>
                    <p class="text-slate-800 font-medium">{{ $checkOut->checked_out_at ? \Carbon\Carbon::parse($checkOut->checked_out_at)->format('M d, Y H:i') : $checkOut->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Checked Out By</p>
                    <p class="text-slate-800 font-medium">{{ $checkOut->checkedOutBy->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Room Condition</p>
                    @if($checkOut->room_condition === 'good')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Good</span>
                    @elseif($checkOut->room_condition === 'needs_cleaning')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Needs Cleaning</span>
                    @elseif($checkOut->room_condition === 'damaged')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Damaged</span>
                    @endif
                </div>
                @if($checkOut->room_condition === 'damaged' && $checkOut->damage_notes)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Damage Notes</p>
                    <p class="text-slate-700 text-sm">{{ $checkOut->damage_notes }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Extra Charges</p>
                    <p class="text-sm font-medium {{ ($checkOut->extra_charges ?? 0) > 0 ? 'text-rose-600' : 'text-slate-800' }}">Rs. {{ number_format($checkOut->extra_charges ?? 0, 0) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Late Check-out</p>
                    @if($checkOut->late_check_out)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Yes</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-50 text-slate-500 border border-slate-200">No</span>
                    @endif
                </div>
                @if($checkOut->notes)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Notes</p>
                    <p class="text-slate-700 text-sm">{{ $checkOut->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($booking->isEditableBy(auth()->user()))
        <a href="{{ route('admin.bookings.edit', $booking->id) }}"
           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 cursor-pointer
                  bg-[#A89070] hover:bg-[#8E795E] text-white rounded-xl
                  font-medium transition-colors shadow-sm">
            <i class="bi bi-pencil-square"></i> Edit Booking
        </a>
        @else
        <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                    text-center text-sm text-slate-400 italic">
            Edit window expired
        </div>
        @endif

        @if(
            $booking->status !== 'checked_in' &&
            $booking->payment_status !== 'refunded' &&
            (($booking->amount_paid ?? 0) > 0 || ($booking->deposit_amount ?? 0) > 0)
        )
        @can('edit bookings')
        <button onclick="openRefundModal()"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 cursor-pointer
                   bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200
                   rounded-xl font-medium transition-colors mt-2">
            <i class="bi bi-arrow-counterclockwise"></i> Process Refund
        </button>
        @endcan
        @endif

    </div>
</div>

@if($canEditDocument)
<div id="verify-id-modal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">{{ $guestDocument ? 'Update' : 'Add' }} Guest ID</h2>
            <button type="button" onclick="closeVerifyIdModal()" class="text-slate-400 cursor-pointer hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="verify-id-form" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Document Type <span class="text-red-500">*</span></label>
                    <select name="document_type" required class="w-full rounded-xl cursor-pointer border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                        <option value="">Select type...</option>
                        <option value="passport"        {{ optional($guestDocument)->document_type === 'passport'        ? 'selected' : (($guest->id_type ?? '') === 'passport'        ? 'selected' : '') }}>Passport</option>
                        <option value="citizenship"     {{ optional($guestDocument)->document_type === 'citizenship'     ? 'selected' : (($guest->id_type ?? '') === 'citizenship'     ? 'selected' : '') }}>Citizenship</option>
                        <option value="driving_license" {{ optional($guestDocument)->document_type === 'driving_license' ? 'selected' : (($guest->id_type ?? '') === 'driving_license' ? 'selected' : '') }}>Driving License</option>
                        <option value="national_id"     {{ optional($guestDocument)->document_type === 'national_id'     ? 'selected' : (($guest->id_type ?? '') === 'national_id'     ? 'selected' : '') }}>National ID</option>
                        <option value="voter_id"        {{ optional($guestDocument)->document_type === 'voter_id'        ? 'selected' : (($guest->id_type ?? '') === 'voter_id'        ? 'selected' : '') }}>Voter ID</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">ID Number <span class="text-red-500">*</span></label>
                    <input type="text" name="id_number" required
                        value="{{ optional($guestDocument)->id_number ?? $guest->id_number }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="e.g. PA1234567">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nationality <span class="text-red-500">*</span></label>
                    <input type="text" name="nationality" required
                        value="{{ optional($guestDocument)->nationality ?? $guest->nationality }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="e.g. Nepali">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_birth" required
                        value="{{ optional($guestDocument)->date_of_birth?->format('Y-m-d') }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ID Photo</label>
                @if(optional($guestDocument)->photo)
                <div class="mb-3 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $guestDocument->photo) }}" alt="ID Photo"
                         class="h-20 w-32 object-cover rounded-lg border border-slate-200">
                    <p class="text-xs text-slate-400">Existing photo — upload a new one to replace it.</p>
                </div>
                @endif
                <input type="file" name="photo" accept="image/*"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-600
                           file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                           file:text-sm file:font-medium file:bg-primary/10 file:text-primary
                           hover:file:bg-primary/20 transition-colors cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary transition-colors resize-none"
                    placeholder="Any observations about the document...">{{ optional($guestDocument)->notes }}</textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeVerifyIdModal()"
                    class="px-6 py-2.5 cursor-pointer rounded-xl font-medium text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl cursor-pointer font-medium bg-primary text-white hover:bg-[#8E795E] transition-colors shadow-sm">
                    Save &amp; Verify
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div id="refund-modal" class="fixed inset-0 z-[100] hidden items-center
     justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0
     transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100
                w-full max-w-md transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">
                Process Refund
            </h2>
            <button onclick="closeRefundModal()"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-600 space-y-1">
                <div class="flex justify-between">
                    <span>Amount Paid</span>
                    <span class="font-medium">Rs. {{ number_format($paid, 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Deposit</span>
                    <span class="font-medium">Rs. {{ number_format($deposit, 0) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-slate-800
                            pt-1 border-t border-slate-200 mt-1">
                    <span>Max Refundable</span>
                    <span>Rs. {{ number_format($paid + $deposit, 0) }}</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Refund Amount (Rs.) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="refund-amount"
                       max="{{ $paid + $deposit }}" min="1" step="1"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3
                              focus:ring-primary focus:border-primary transition-colors"
                       placeholder="Enter refund amount">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Notes (optional)
                </label>
                <textarea id="refund-notes" rows="3"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3
                           focus:ring-primary focus:border-primary transition-colors resize-none"
                    placeholder="Reason for refund..."></textarea>
            </div>
        </div>
        <div class="p-6 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeRefundModal()"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium
                           text-slate-600 hover:bg-slate-100 transition-colors">
                Cancel
            </button>
            <button onclick="submitRefund()"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium
                           bg-rose-600 hover:bg-rose-700 text-white
                           transition-colors shadow-sm">
                Confirm Refund
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
@if($canEditDocument)
function openVerifyIdModal() {
    const modal = document.getElementById('verify-id-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }, 10);
}

function closeVerifyIdModal() {
    const modal = document.getElementById('verify-id-modal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

document.getElementById('verify-id-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('guest_id', '{{ $guest->id }}');

    try {
        const response = await fetch('/api/guest-documents', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
        });
        const result = await response.json();
        if (response.ok && result.success) {
            adminToast('ID document saved successfully.', 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            adminToast(result.message ?? 'Failed to save document.', 'error');
        }
    } catch (err) {
        adminToast('An error occurred while saving the document.', 'error');
    }
});

document.getElementById('verify-id-modal').addEventListener('click', function(e) {
    if (e.target === this) closeVerifyIdModal();
});
@endif
</script>
<script>
function openRefundModal() {
    const modal = document.getElementById('refund-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeRefundModal() {
    const modal = document.getElementById('refund-modal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function submitRefund() {
    const amount = document.getElementById('refund-amount').value;
    const notes  = document.getElementById('refund-notes').value;

    if (!amount || parseFloat(amount) <= 0) {
        adminToast('Please enter a valid refund amount.', 'error');
        return;
    }

    if (!await adminConfirm(
        `Process a refund of Rs. ${parseFloat(amount).toLocaleString('en-IN')}? This cannot be undone.`,
        { confirmLabel: 'Confirm Refund', type: 'danger' }
    )) return;

    try {
        const response = await fetch(`/api/bookings/{{ $booking->id }}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ refund_amount: amount, notes }),
        });

        const data = await response.json();
        if (data.success) {
            adminToast('Refund processed successfully.', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            adminToast(data.message ?? 'Refund failed.', 'error');
        }
    } catch (e) {
        adminToast('An error occurred.', 'error');
    }
}

document.getElementById('refund-modal')
    .addEventListener('click', function(e) {
        if (e.target === this) closeRefundModal();
    });
</script>
@endpush
