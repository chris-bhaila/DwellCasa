<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'guest_id' => 'sometimes|nullable|exists:guests,id',
            'booking_ref' => 'sometimes|required|string|max:255|unique:bookings,booking_ref,' . $this->route('booking'),
            'guest_name' => 'sometimes|required|string|max:255',
            'guest_email' => 'sometimes|required|email|max:255',
            'guest_phone' => 'nullable|string|max:30',
            'room_type' => 'sometimes|required_without:room_type_id|string|max:255',
            'room_type_id' => 'sometimes|required_without:room_type|exists:room_types,id',
            'check_in_date' => 'sometimes|required|date',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'message' => 'nullable|string',
            'num_guests' => 'nullable|integer|min:1',
            'stay_type' => 'nullable|string|in:short_term,long_term',
            'total_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'rate_per_night' => 'sometimes|required_if:stay_type,short_term|nullable|numeric|min:0',
            'rate_per_month' => 'sometimes|required_if:stay_type,long_term|nullable|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'special_requests' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $totalAmount = floatval($this->input('total_amount') ?? 0);
            $discount    = floatval($this->input('discount') ?? 0);
            $amountPaid  = floatval($this->input('amount_paid') ?? 0);
            $netAmount   = $totalAmount - $discount;

            if ($discount > $totalAmount) {
                $validator->errors()->add(
                    'discount',
                    'Discount cannot exceed the total amount (Rs. ' . number_format($totalAmount, 0) . ').'
                );
            }

            if ($amountPaid > $netAmount) {
                $validator->errors()->add(
                    'amount_paid',
                    'Amount paid cannot exceed the net amount (Rs. ' . number_format($netAmount, 0) . ').'
                );
            }
        });
    }

    protected function prepareForValidation()
    {
        $merge = [];

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
        $name = $this->guest_name ?? $this->name ?? null;
        $phone = $this->guest_phone ?? $this->phone ?? null;

        if ($email && $name) {
            $locationId = \App\Models\Booking::where('id', $this->route('booking'))->value('location_id');

            $guest = \App\Models\Guest::updateOrCreate(
                ['email' => $email, 'location_id' => $locationId],
                ['full_name' => $name, 'phone' => $phone, 'location_id' => $locationId]
            );
            $merge['guest_id'] = $guest->id;
        }

        $this->request->remove('payment_status');

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}