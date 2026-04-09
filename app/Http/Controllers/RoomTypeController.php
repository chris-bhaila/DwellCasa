<?php

namespace App\Http\Controllers;

use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;

class RoomTypeController extends Controller
{
    protected $roomTypeRepository;

    public function __construct(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $this->roomTypeRepository = $roomTypeRepository;
    }

    public function index()
    {
        $roomTypes = $this->roomTypeRepository->all();
        return response()->json([
            'data' => $roomTypes,
            'message' => 'Room types fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $roomType = $this->roomTypeRepository->find($id);
        return response()->json([
            'data' => $roomType,
            'message' => 'Room type fetched successfully'
        ], 200);
    }

    public function store(StoreRoomTypeRequest $request)
    {
        $roomType = $this->roomTypeRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully',
            'data' => $roomType
        ], 201);
    }

    public function update(UpdateRoomTypeRequest $request, $id)
    {
        $roomType = $this->roomTypeRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Room type updated successfully',
            'data' => $roomType
        ], 200);
    }

    public function destroy($id)
    {
        $this->roomTypeRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Room type deleted successfully'
        ], 200);
    }
}