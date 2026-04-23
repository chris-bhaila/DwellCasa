<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'sometimes|string|max:255',
            'category'       => 'sometimes|in:housekeeping,toiletries,food_beverage,maintenance,office,other',
            'stock'          => 'sometimes|numeric|min:0',
            'unit'           => 'sometimes|in:pieces,kg,liters,boxes,rolls',
            'unit_condition' => 'sometimes|in:new,good,fair,poor,damaged',
            'minimum_stock'  => 'sometimes|numeric|min:0',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'unit_price' => 'nullable|numeric|min:0',
        ];
    }
}