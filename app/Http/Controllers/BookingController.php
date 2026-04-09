<?php

namespace App\Http\Controllers;

use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingRepository->all();
        return response()->json([
            'data' => $bookings,
            'message' => 'Bookings fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $booking = $this->bookingRepository->find($id);
        return response()->json([
            'data' => $booking,
            'message' => 'Booking fetched successfully'
        ], 200);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = $this->bookingRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking
        ], 200);
    }

    public function destroy($id)
    {
        $this->bookingRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ], 200);
    }
}
