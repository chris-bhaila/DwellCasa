<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => 'nullable|exists:rooms,id',
            'checked_in_by' => 'nullable|exists:users,id',
            'checked_in_at' => 'date',
            'early_check_in' => 'boolean',
            'id_verified' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}