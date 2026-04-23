<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'category'       => 'required|in:housekeeping,toiletries,food_beverage,maintenance,office,other',
            'stock'          => 'required|numeric|min:0',
            'unit'           => 'required|in:pieces,kg,liters,boxes,rolls',
            'unit_condition' => 'required|in:new,good,fair,poor,damaged',
            'minimum_stock'  => 'required|numeric|min:0',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'unit_price' => 'nullable|numeric|min:0',
        ];
    }
}