<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UseSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:0.01',
            'room_id'  => 'nullable|integer|exists:rooms,id',
            'notes'    => 'nullable|string',
        ];
    }
}
