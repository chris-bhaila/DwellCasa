<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inquiry_ref' => 'sometimes|required|string|max:255|unique:booking_inquiries,inquiry_ref,' . $this->route('booking_inquiry'),
            'guest_id' => 'nullable|exists:guests,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'guest_name' => 'sometimes|required|string|max:255',
            'guest_email' => 'sometimes|required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'check_in_date' => 'sometimes|required|date',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'num_guests' => 'sometimes|required|integer|min:1',
            'stay_type' => 'sometimes|required|in:short_term,long_term',
            'message' => 'nullable|string',
            'status' => 'in:pending,reviewed,confirmed,cancelled,expired',
            'admin_notes' => 'nullable|string',
            'responded_at' => 'nullable|date',
            'ip_address' => 'nullable|ip',
        ];
    }
}