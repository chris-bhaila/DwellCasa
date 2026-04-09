<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inquiry_ref' => 'required|string|max:255|unique:booking_inquiries,inquiry_ref',
            'guest_id' => 'nullable|exists:guests,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_guests' => 'required|integer|min:1',
            'stay_type' => 'required|in:short_term,long_term',
            'message' => 'nullable|string',
            'status' => 'in:pending,reviewed,confirmed,cancelled,expired',
            'admin_notes' => 'nullable|string',
            'responded_at' => 'nullable|date',
            'ip_address' => 'nullable|ip',
        ];
    }
}