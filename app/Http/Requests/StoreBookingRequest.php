<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Guest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_ref'     => 'required|string|unique:bookings,booking_ref',
            'guest_id'        => 'nullable|exists:guests,id',
            'room_type_id'    => 'required|exists:room_types,id',
            'check_in_date'   => 'required|date|after_or_equal:today',
            'check_out_date'  => 'required|date|after:check_in_date',
            'num_guests'      => 'required|integer|min:1',
            'stay_type'       => 'required|in:short_term,long_term',
            'guest_name'      => 'required|string|max:255',
            'guest_email'     => 'required|email|max:255',
            'guest_phone'      => 'nullable|string|max:30',
            'status'           => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'rate_per_night'   => 'nullable|numeric|min:0',
            'rate_per_month'   => 'nullable|numeric|min:0',
            'total_amount'     => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'deposit_amount'   => 'nullable|numeric|min:0',
            'amount_paid'      => 'nullable|numeric|min:0',
            'payment_status'   => 'nullable|in:unpaid,deposit_paid,partially_paid,fully_paid,refunded',
            'special_requests' => 'nullable|string',
            'admin_notes'      => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $email = $this->guest_email;
        $name  = $this->guest_name ?? 'Guest';
        $phone = $this->guest_phone ?? null;

        $merge = [
            'booking_ref' => 'BKG-' . strtoupper(Str::random(8)),
            'amount_paid' => $this->amount_paid ?? null,
        ];

        if ($email) {
            $user = auth()->user();
            if ($user) {
                $locationId = $user->hasRole('super_admin')
                    ? session('selected_location_id')
                    : $user->location_id;
            } else {
                $locationId = $this->route('location')?->id;
            }

            $guest = Guest::firstOrCreate(
                ['email' => $email, 'location_id' => $locationId],
                ['full_name' => $name, 'phone' => $phone, 'location_id' => $locationId]
            );
            $merge['guest_id'] = $guest->id;
        }

        $this->merge($merge);
    }
}
