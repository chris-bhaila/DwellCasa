<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:property_settings,key',
            'group' => 'required|string|max:255',
            'value' => 'nullable',
            'type' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
        ];
    }
}