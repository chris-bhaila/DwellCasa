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
            'guest_phone'     => 'nullable|string|max:20',
            'special_requests'=> 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $email = $this->guest_email;
        $name  = $this->guest_name ?? 'Guest';
        $phone = $this->guest_phone ?? null;

        $merge = [
            'booking_ref' => 'BKG-' . strtoupper(Str::random(8)),
        ];

        if ($email) {
            $guest = Guest::firstOrCreate(
                ['email' => $email],
                ['full_name' => $name, 'phone' => $phone]
            );
            $merge['guest_id'] = $guest->id;
        }

        $this->merge($merge);
    }
}