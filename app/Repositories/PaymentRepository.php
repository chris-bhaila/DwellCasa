<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function all()
    {
        return Payment::with(['booking', 'guest'])->latest()->get();
    }

    public function find($id)
    {
        return Payment::with(['booking', 'guest'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Payment::create($data);
    }

    public function update($id, array $data)
    {
        $payment = $this->find($id);
        $payment->update($data);
        return $payment;
    }

    public function delete($id)
    {
        $payment = $this->find($id);
        $payment->delete();
        return true;
    }
}