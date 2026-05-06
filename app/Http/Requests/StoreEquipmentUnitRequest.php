<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => 'required|integer|exists:inventory_items,id',
            'serial_number'     => 'nullable|string|max:255',
            'condition'         => 'required|in:new,good,fair,damaged,under_repair',
            'status'            => 'required|in:available,assigned,maintenance,retired',
            'purchased_at'      => 'nullable|date',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ];
    }
}
