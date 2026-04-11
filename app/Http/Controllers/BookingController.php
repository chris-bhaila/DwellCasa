<?php

namespace App\Http\Controllers;

use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $status = $request->input('status', 'pending');
        
        if (in_array($status, ['pending', 'confirmed', 'checked_in'])) {
            $error = $this->checkAvailability($request->room_type_id, $request->check_in_date, $request->check_out_date);
            if ($error) {
                return response()->json(['message' => $error, 'errors' => ['room_type_id' => [$error]]], 422);
            }
        }

        $booking = $this->bookingRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $roomTypeId = $request->input('room_type_id', $booking->room_type_id);
        $checkIn = $request->input('check_in_date', $booking->check_in_date);
        $checkOut = $request->input('check_out_date', $booking->check_out_date);
        $status = $request->input('status', $booking->status);

        if (in_array($status, ['pending', 'confirmed', 'checked_in'])) {
            $error = $this->checkAvailability($roomTypeId, $checkIn, $checkOut, $id);
            if ($error) {
                return response()->json(['message' => $error, 'errors' => ['room_type_id' => [$error]]], 422);
            }
        }

        $updatedBooking = $this->bookingRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $updatedBooking
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

    private function checkAvailability($roomTypeId, $checkIn, $checkOut, $excludeBookingId = null)
    {
        $roomType = RoomType::withCount('rooms')->findOrFail($roomTypeId);

        if ($roomType->rooms_count === 0) {
            return 'Sorry, no physical rooms have been added for this room type yet.';
        }

        $query = Booking::where('room_type_id', $roomTypeId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $bookings = $query->get(['check_in_date', 'check_out_date']);
        $period = CarbonPeriod::create($checkIn, Carbon::parse($checkOut)->subDay());

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $overlappingCount = $bookings->filter(function ($b) use ($dateStr) {
                return $dateStr >= Carbon::parse($b->check_in_date)->format('Y-m-d') 
                    && $dateStr < Carbon::parse($b->check_out_date)->format('Y-m-d');
            })->count();

            if ($overlappingCount >= $roomType->rooms_count) {
                return 'Sorry, this room type is fully booked on ' . $date->format('M j, Y') . '.';
            }
        }

        return null;
    }
}
