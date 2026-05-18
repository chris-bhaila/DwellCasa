<?php

namespace App\Http\Controllers;

use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
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
            ->log("Created room {$room->room_number}" . ($room->room_name ? " — {$room->room_name}" : ''));
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
            ->log("Updated room {$room->room_number} — status: {$room->status}");
        return response()->json(['success' => true, 'message' => 'Room updated successfully', 'data' => $room], 200);
    }

    public function destroy($id)
    {
        $room = $this->roomRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $room->location_id])
            ->log("Deleted room {$room->room_number}");
        $this->roomRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Room deleted successfully'], 200);
    }

    public function trashed()
    {
        $rooms = $this->roomRepository->trashed();
        return response()->json([
            'data'    => $rooms,
            'message' => 'Trashed rooms fetched successfully'
        ], 200);
    }

    public function restore($id)
    {
        $room = $this->roomRepository->restore($id);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($room)
            ->withProperties(['location_id' => $room->location_id])
            ->log("Restored room {$room->room_number}");
        return response()->json([
            'success' => true,
            'message' => 'Room restored successfully',
            'data'    => $room
        ], 200);
    }

    public function forceDelete($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $room->location_id])
            ->log("Permanently deleted room {$room->room_number}");
        $this->roomRepository->forceDelete($id);
        return response()->json([
            'success' => true,
            'message' => 'Room permanently deleted'
        ], 200);
    }

    public function createPage(AmenityRepositoryInterface $amenityRepository)
    {
        $roomTypes = RoomType::withCount('rooms')->where('is_active', true)->orderBy('name')->get();
        $amenities = $amenityRepository->all();

        return view('admin.room_type.room.add-room', compact('roomTypes', 'amenities'));
    }

    public function editPage(int $id, AmenityRepositoryInterface $amenityRepository)
    {
        $room      = Room::findOrFail($id);
        $amenities = $amenityRepository->all();
        $roomTypes = RoomType::withCount('rooms')->where('is_active', true)->orderBy('name')->get();

        $activeBooking = Booking::with('guest')
            ->where('status', 'checked_in')
            ->where('room_id', $room->id)
            ->first();

        if ($activeBooking) {
            $room->status = 'occupied';
        }

        return view('admin.room_type.room.edit-room', compact('room', 'roomTypes', 'amenities', 'activeBooking'));
    }
}
