<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'sometimes|required|exists:room_types,id',
            'room_number' => 'sometimes|required|string|max:255|unique:rooms,room_number,' . $this->route('room'),
            'floor' => 'nullable|string|max:100',
            'status' => 'sometimes|required|in:available,occupied,maintenance,reserved',
            'notes' => 'nullable|string',
        ];
    }
}