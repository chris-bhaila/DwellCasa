<?php

namespace App\Http\Controllers;

use App\Contracts\RoomRepositoryInterface;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;

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
        $room = $this->roomRepository->create($request->validated());
        return response()->json(['success' => true, 'message' => 'Room created successfully', 'data' => $room], 201);
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $room = $this->roomRepository->update($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Room updated successfully', 'data' => $room], 200);
    }

    public function destroy($id)
    {
        $this->roomRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Room deleted successfully'], 200);
    }
}