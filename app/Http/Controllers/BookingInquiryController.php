<?php

namespace App\Http\Controllers;

use App\Contracts\BookingInquiryRepositoryInterface;
use App\Http\Requests\StoreBookingInquiryRequest;
use App\Http\Requests\UpdateBookingInquiryRequest;

class BookingInquiryController extends Controller
{
    protected $bookingInquiryRepository;

    public function __construct(BookingInquiryRepositoryInterface $bookingInquiryRepository)
    {
        $this->bookingInquiryRepository = $bookingInquiryRepository;
    }

    public function index()
    {
        $inquiries = $this->bookingInquiryRepository->all();
        return response()->json([
            'data' => $inquiries,
            'message' => 'Booking inquiries fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $inquiry = $this->bookingInquiryRepository->find($id);
        return response()->json([
            'data' => $inquiry,
            'message' => 'Booking inquiry fetched successfully'
        ], 200);
    }

    public function store(StoreBookingInquiryRequest $request)
    {
        $inquiry = $this->bookingInquiryRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking inquiry created successfully',
            'data' => $inquiry
        ], 201);
    }

    public function update(UpdateBookingInquiryRequest $request, $id)
    {
        $inquiry = $this->bookingInquiryRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking inquiry updated successfully',
            'data' => $inquiry
        ], 200);
    }

    public function destroy($id)
    {
        $this->bookingInquiryRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Booking inquiry deleted successfully'
        ], 200);
    }
}