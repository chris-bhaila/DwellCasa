<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locationId = $this->route('location') ?? $this->route('id');

        return [
            'name'        => 'sometimes|string|max:255',
            'slug'        => 'sometimes|string|max:255|unique:locations,slug,' . $locationId,
            'description' => 'nullable|string',
            'hero_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'address'     => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'is_active'   => 'boolean',
        ];
    }
}