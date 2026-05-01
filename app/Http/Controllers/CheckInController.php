<?php

namespace App\Http\Controllers;

use App\Contracts\CheckInRepositoryInterface;
use App\Http\Requests\StoreCheckInRequest;
use App\Http\Requests\UpdateCheckInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\Activity;

class CheckInController extends Controller
{
    protected $checkInRepository;

    public function __construct(CheckInRepositoryInterface $checkInRepository)
    {
        $this->checkInRepository = $checkInRepository;
    }

    public function index(Request $request)
    {
        $checkIns = $this->checkInRepository->all();
        return response()->json([
            'data' => $checkIns,
            'message' => 'Check-ins fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $checkIn = $this->checkInRepository->find($id);
        return response()->json([
            'data' => $checkIn,
            'message' => 'Check-in fetched successfully'
        ], 200);
    }

    public function store(StoreCheckInRequest $request)
    {
        $checkIn = DB::transaction(function () use ($request) {
            $checkIn = $this->checkInRepository->create($request->validated());

            $checkIn->booking->update([
                'status' => 'checked_in',
                'room_id' => $checkIn->room_id
            ]);

            if ($checkIn->room_id) {
                \App\Models\Room::where('id', $checkIn->room_id)
                    ->update(['status' => 'occupied']);
            }

            return $checkIn;
        });

        // Log AFTER transaction succeeds
        activity()
            ->causedBy(auth()->user())
            ->performedOn($checkIn->booking)
            ->withProperties(['location_id' => $checkIn->booking->location_id])
            ->log('Checked in guest ' . ($checkIn->booking->guest->full_name ?? $checkIn->booking->guest_name) . ' — Room ' . $checkIn->room->room_number . ' (' . $checkIn->booking->booking_ref . ')');

        return response()->json([
            'success' => true,
            'message' => 'Check-in created successfully',
            'data' => $checkIn
        ], 201);
    }

    public function update(UpdateCheckInRequest $request, $id)
    {
        $checkIn = $this->checkInRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Check-in updated successfully',
            'data' => $checkIn
        ], 200);
    }

    public function destroy($id)
    {
        $this->checkInRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Check-in deleted successfully'
        ], 200);
    }
}
