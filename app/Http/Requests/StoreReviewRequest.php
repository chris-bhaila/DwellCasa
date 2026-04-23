<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'rating'       => 'required|integer|min:1|max:5',
            'title'        => 'nullable|string|max:255',
            'body'         => 'required|string',
            'type'         => 'required|in:hotel,room_type',
            'booking_id'   => 'nullable|exists:bookings,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'guest_id'     => 'nullable|exists:guests,id',
        ];
    }
}