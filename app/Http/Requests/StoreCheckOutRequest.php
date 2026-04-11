<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'room_id' => 'nullable|exists:rooms,id',
            'checked_out_by' => 'nullable|exists:users,id',
            'checked_out_at' => 'required|date',
            'late_check_out' => 'boolean',
            'room_condition' => 'required|in:good,damaged,needs_cleaning',
            'damage_notes' => 'nullable|string|required_if:room_condition,damaged',
            'extra_charges' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}