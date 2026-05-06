<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:0.01',
            'cost'     => 'required|numeric|min:0',
            'notes'    => 'nullable|string',
        ];
    }
}
