<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'   => 'sometimes|integer|exists:inventory_categories,id',
            'name'          => 'sometimes|string|max:255',
            'unit'          => 'sometimes|nullable|string|max:100',
            'minimum_stock' => 'sometimes|nullable|numeric|min:0',
            'description'   => 'sometimes|nullable|string',
            'image'         => 'sometimes|nullable|string|max:255',
        ];
    }
}
