<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'sometimes|required|in:daily,weekly,on_request,included',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}