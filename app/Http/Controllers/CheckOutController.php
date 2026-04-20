<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Contracts\CheckOutRepositoryInterface;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    protected $checkOutRepository;

    public function __construct(CheckOutRepositoryInterface $checkOutRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
    }

    public function index(Request $request)
    {
        $checkOuts = $this->checkOutRepository->all();
        return response()->json([
            'data' => $checkOuts,
            'message' => 'Check-outs fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $checkOut = $this->checkOutRepository->find($id);
        return response()->json([
            'data' => $checkOut,
            'message' => 'Check-out fetched successfully'
        ], 200);
    }

    public function store(StoreCheckOutRequest $request)
    {
        try {
            $checkOut = DB::transaction(function () use ($request) {
                $validated = $request->validated();

                // 1. Create a record in check_outs table using the repository pattern
                $checkOutRecord = $this->checkOutRepository->create($validated);

                // 2. Update bookings.status to checked_out and set checked_out_at
                $booking = Booking::findOrFail($validated['booking_id']);
                $booking->status = 'checked_out';
                $booking->checked_out_at = $validated['checked_out_at'];
                $booking->save();

                // 3. Update rooms.status back to available using the room_id from the booking
                if ($booking->room_id) {
                    $room = Room::findOrFail($booking->room_id);
                    $room->status = 'available';
                    $room->save();
                }

                return $checkOutRecord;
            });

            return response()->json([
                'message' => 'Guest successfully checked out.',
                'data' => $checkOut
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-out failed due to an internal error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCheckOutRequest $request, $id)
    {
        $checkOut = $this->checkOutRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Check-out updated successfully',
            'data' => $checkOut
        ], 200);
    }

    public function destroy($id)
    {
        $this->checkOutRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Check-out deleted successfully'
        ], 200);
    }
}
