<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:255|unique:rooms,room_number',
            'floor' => 'nullable|string|max:100',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'notes' => 'nullable|string',
        ];
    }
}