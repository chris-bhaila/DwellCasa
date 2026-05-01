<?php

namespace App\Http\Controllers;

use App\Contracts\AmenityRepositoryInterface;
use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\UpdateAmenityRequest;

class AmenityController extends Controller
{
    protected $amenityRepository;

    public function __construct(AmenityRepositoryInterface $amenityRepository)
    {
        $this->amenityRepository = $amenityRepository;
    }

    public function index()
    {
        $amenities = $this->amenityRepository->all();
        return response()->json(['data' => $amenities, 'message' => 'Amenities fetched successfully'], 200);
    }

    public function show($id)
    {
        $amenity = $this->amenityRepository->find($id);
        return response()->json(['data' => $amenity, 'message' => 'Amenity fetched successfully'], 200);
    }

    public function store(StoreAmenityRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $data = $request->validated();
        $data['location_id'] = $locationId;

        $amenity = $this->amenityRepository->create($data);
        return response()->json(['success' => true, 'message' => 'Amenity created successfully', 'data' => $amenity], 201);
    }

    public function update(UpdateAmenityRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']);

        $amenity = $this->amenityRepository->update($id, $data);
        return response()->json(['success' => true, 'message' => 'Amenity updated successfully', 'data' => $amenity], 200);
    }

    public function destroy($id)
    {
        $this->amenityRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Amenity deleted successfully'], 200);
    }
}
