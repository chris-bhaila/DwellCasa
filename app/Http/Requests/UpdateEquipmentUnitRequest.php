<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serial_number' => 'sometimes|nullable|string|max:255',
            'purchased_at'  => 'sometimes|nullable|date',
            'purchase_cost' => 'sometimes|nullable|numeric|min:0',
            'notes'         => 'sometimes|nullable|string',
        ];
    }
}
