<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_id' => 'nullable|exists:guests,id',
            'booking_ref' => 'required|string|max:255|unique:bookings,booking_ref',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'room_type' => 'required_without:room_type_id|string|max:255',
            'room_type_id' => 'required_without:room_type|exists:room_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'message' => 'nullable|string',
            'num_guests' => 'nullable|integer|min:1',
            'stay_type' => 'nullable|string|in:short_term,long_term',
            'total_amount' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string',
            'rate_per_night' => 'required_if:stay_type,short_term|nullable|numeric|min:0',
            'rate_per_month' => 'required_if:stay_type,long_term|nullable|numeric|min:0',
            'status' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        $merge = [];

        if (!$this->booking_ref) {
            $merge['booking_ref'] = 'BKG-' . strtoupper(\Illuminate\Support\Str::random(8));
        }

        if (!$this->guest_name && $this->name) {
            $merge['guest_name'] = $this->name;
        }
        if (!$this->guest_email && $this->email) {
            $merge['guest_email'] = $this->email;
        }
        if (!$this->guest_phone && $this->phone) {
            $merge['guest_phone'] = $this->phone;
        }
        if (!$this->check_in_date && $this->check_in) {
            $merge['check_in_date'] = $this->check_in;
        }
        if (!$this->check_out_date && $this->check_out) {
            $merge['check_out_date'] = $this->check_out;
        }

        // Auto-create or find guest and attach guest_id
        $email = $this->guest_email ?? $this->email ?? null;
        $name = $this->guest_name ?? $this->name ?? 'Guest';
        $phone = $this->guest_phone ?? $this->phone ?? null;

        if ($email) {
            $guest = \App\Models\Guest::firstOrCreate(
                ['email' => $email],
                ['full_name' => $name, 'phone' => $phone]
            );
            $merge['guest_id'] = $guest->id;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
