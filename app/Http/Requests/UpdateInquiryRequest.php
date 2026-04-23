<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'inquiry_type' => 'sometimes|in:general,booking,amenities,pricing,other',
            'message'      => 'sometimes|string',
            'status'       => 'sometimes|in:unreplied,replied',
        ];
    }
}