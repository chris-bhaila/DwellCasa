<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_ref' => 'required|string|max:255|unique:payments,transaction_ref',
            'booking_id' => 'required|exists:bookings,id',
            'guest_id' => 'required|exists:guests,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|in:cash,bank_transfer,esewa,khalti,card,other',
            'type' => 'required|in:deposit,rent,refund,fee',
            'status' => 'required|in:pending,completed,failed,refunded',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ];
    }
}