<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condition' => 'required|in:new,good,fair,damaged,under_repair',
            'notes'     => 'nullable|string',
        ];
    }
}
