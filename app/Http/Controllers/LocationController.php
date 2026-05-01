<?php

namespace App\Http\Controllers;

use App\Contracts\LocationRepositoryInterface;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    protected $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function index()
    {
        $locations = $this->locationRepository->all();
        return response()->json([
            'data'    => $locations,
            'message' => 'Locations fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $location = $this->locationRepository->find($id);
        return response()->json([
            'data'    => $location,
            'message' => 'Location fetched successfully'
        ], 200);
    }

    public function store(StoreLocationRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $filename = Str::slug($data['name']) . '_hero.' . $file->getClientOriginalExtension();
            $data['hero_image'] = $file->storeAs('locations', $filename, 'public');
        }

        $location = $this->locationRepository->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data'    => $location
        ], 201);
    }

    public function update(UpdateLocationRequest $request, $id)
    {
        $data = $request->validated();
        $location = $this->locationRepository->find($id);

        if ($request->hasFile('hero_image')) {
            if ($location->hero_image && Storage::disk('public')->exists($location->hero_image)) {
                Storage::disk('public')->delete($location->hero_image);
            }
            $file = $request->file('hero_image');
            $filename = Str::slug($data['name'] ?? $location->name) . '_hero.' . $file->getClientOriginalExtension();
            $data['hero_image'] = $file->storeAs('locations', $filename, 'public');
        }

        $location = $this->locationRepository->update($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data'    => $location
        ], 200);
    }

    public function destroy($id)
    {
        $this->locationRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ], 200);
    }
}