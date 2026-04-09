<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|required|string|max:255|unique:property_settings,key,' . $this->route('property_setting'),
            'group' => 'sometimes|required|string|max:255',
            'value' => 'nullable',
            'type' => 'sometimes|required|string|max:255',
            'label' => 'nullable|string|max:255',
        ];
    }
}