<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentRepositoryInterface;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;

class PaymentController extends Controller
{
    protected $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index()
    {
        $payments = $this->paymentRepository->all();
        return response()->json([
            'data' => $payments,
            'message' => 'Payments fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $payment = $this->paymentRepository->find($id);
        return response()->json([
            'data' => $payment,
            'message' => 'Payment fetched successfully'
        ], 200);
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    public function update(UpdatePaymentRequest $request, $id)
    {
        $payment = $this->paymentRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment
        ], 200);
    }

    public function destroy($id)
    {
        $this->paymentRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ], 200);
    }
}