<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'   => 'required|integer|exists:inventory_categories,id',
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:supply,equipment',
            'unit'          => 'required_if:type,supply|nullable|string|max:100',
            'minimum_stock' => 'required_if:type,supply|nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'image'         => 'nullable|string|max:255',
        ];
    }
}
