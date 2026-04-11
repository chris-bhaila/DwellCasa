<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckInRequest extends FormRequest
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
            'checked_in_by' => 'nullable|exists:users,id',
            'checked_in_at' => 'required|date',
            'early_check_in' => 'boolean',
            'id_verified' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}