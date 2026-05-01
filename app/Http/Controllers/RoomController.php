<?php

namespace App\Http\Controllers;

use App\Contracts\RoomRepositoryInterface;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Spatie\Activitylog\Facades\Activity;
class RoomController extends Controller
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function index()
    {
        $rooms = $this->roomRepository->all();
        return response()->json(['data' => $rooms, 'message' => 'Rooms fetched successfully'], 200);
    }

    public function show($id)
    {
        $room = $this->roomRepository->find($id);
        return response()->json(['data' => $room, 'message' => 'Room fetched successfully'], 200);
    }

    public function store(StoreRoomRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $data = $request->validated();
        $data['location_id'] = $locationId;

        $room = $this->roomRepository->create($data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($room)
            ->withProperties(['location_id' => $locationId])
            ->log('Created room ' . $room->room_number . ($room->room_name ? ' — ' . $room->room_name : ''));
        return response()->json(['success' => true, 'message' => 'Room created successfully', 'data' => $room], 201);
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']);

        $room = $this->roomRepository->update($id, $data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($room)
            ->withProperties(['location_id' => $room->location_id])
            ->log('Updated room ' . $room->room_number . ' — status: ' . $room->status);
        return response()->json(['success' => true, 'message' => 'Room updated successfully', 'data' => $room], 200);
    }

    public function destroy($id)
    {
        $room = $this->roomRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $room->location_id])
            ->log('Deleted room ' . $room->room_number);
        $this->roomRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Room deleted successfully'], 200);
    }
}
