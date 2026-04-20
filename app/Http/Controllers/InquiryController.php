<?php

namespace App\Http\Controllers;

use App\Contracts\InquiryRepositoryInterface;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryRepository;

    public function __construct(InquiryRepositoryInterface $inquiryRepository)
    {
        $this->inquiryRepository = $inquiryRepository;
    }

    public function index(Request $request)
    {
        $inquiries = $this->inquiryRepository->all();
        return response()->json([
            'data' => $inquiries,
            'message' => 'Inquiries fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $inquiry = $this->inquiryRepository->find($id);
        return response()->json([
            'data' => $inquiry,
            'message' => 'Inquiry fetched successfully'
        ], 200);
    }

    public function store(StoreInquiryRequest $request)
    {
        $inquiry = $this->inquiryRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'data' => $inquiry
        ], 201);
    }

    public function update(UpdateInquiryRequest $request, $id)
    {
        $inquiry = $this->inquiryRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data' => $inquiry
        ], 200);
    }

    public function destroy($id)
    {
        $this->inquiryRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully'
        ], 200);
    }
}