<?php

namespace App\Http\Controllers;

use App\Contracts\LocationRepositoryInterface;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Booking;
use App\Models\User;
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
        $id = $id instanceof \App\Models\Location ? $id->getKey() : $id;
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
        $id = $id instanceof \App\Models\Location ? $id->getKey() : $id;
        $data = $request->validated();
        $location = $this->locationRepository->find($id);

        if ($request->hasFile('hero_image')) {
            if ($location->hero_image && Storage::disk('public')->exists($location->hero_image)) {
                Storage::disk('public')->delete($location->hero_image);
            }
            $file = $request->file('hero_image');
            $filename = Str::slug($data['name'] ?? $location->name) . '_hero.' . $file->getClientOriginalExtension();
            $data['hero_image'] = $file->storeAs('locations', $filename, 'public');
        } else {
            unset($data['hero_image']);
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
        $id = $id instanceof \App\Models\Location ? $id->getKey() : $id;

        $activeBookings = Booking::where('location_id', $id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete: this location has {$activeBookings} active booking(s). Cancel or complete them first.",
            ], 422);
        }

        $assignedUsers = User::where('location_id', $id)->count();

        if ($assignedUsers > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete: {$assignedUsers} user(s) are assigned to this location. Reassign them first.",
            ], 422);
        }

        $this->locationRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ], 200);
    }

    public function page()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $locations = $this->locationRepository->all();
        } else {
            $locations = \App\Models\Location::where('id', $user->location_id)->get();
        }

        return view('admin.location', compact('locations'));
    }
}