<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'max_occupancy' => 'sometimes|required|integer|min:1',
            'price_per_night' => 'nullable|numeric|min:0',
            'price_per_month' => 'nullable|numeric|min:0',
            'size_sqft' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'is_standalone' => 'boolean',
        ];
    }
}