<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'imageable_type' => 'required|string',
            'imageable_id' => 'required|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}