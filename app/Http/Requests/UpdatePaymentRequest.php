<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_ref' => 'sometimes|required|string|max:255|unique:payments,transaction_ref,' . $this->route('payment'),
            'booking_id' => 'sometimes|required|exists:bookings,id',
            'guest_id' => 'sometimes|required|exists:guests,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|size:3',
            'payment_method' => 'sometimes|required|in:cash,bank_transfer,esewa,khalti,card,other',
            'type' => 'sometimes|required|in:deposit,rent,refund,fee',
            'status' => 'sometimes|required|in:pending,completed,failed,refunded',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ];
    }
}