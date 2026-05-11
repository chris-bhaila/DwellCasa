<?php

namespace App\Http\Controllers;

use App\Contracts\AmenityRepositoryInterface;
use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\UpdateAmenityRequest;
use App\Models\Amenity;
use App\Models\Location;
use Illuminate\Http\Request;

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

    public function importFrom(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $request->validate(['source_location_id' => 'required|integer|exists:locations,id']);

        $targetLocationId = session('selected_location_id');
        abort_if(!$targetLocationId, 422, 'No location selected.');
        abort_if($request->source_location_id == $targetLocationId, 422, 'Source and target location are the same.');

        $source = Amenity::withoutGlobalScopes()
            ->where('location_id', $request->source_location_id)
            ->get();

        $existing = Amenity::withoutGlobalScopes()
            ->where('location_id', $targetLocationId)
            ->get();

        foreach ($existing as $amenity) {
            $amenity->roomTypes()->detach();
            $amenity->delete();
        }

        foreach ($source as $amenity) {
            Amenity::withoutGlobalScopes()->create([
                'name'        => $amenity->name,
                'icon'        => $amenity->icon,
                'category'    => $amenity->category instanceof \App\Enums\AmenityCategory
                    ? $amenity->category->value
                    : $amenity->category,
                'description' => $amenity->description,
                'is_active'   => $amenity->is_active,
                'sort_order'  => $amenity->sort_order,
                'location_id' => $targetLocationId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$source->count()} amenities successfully. Previous amenities and room type assignments have been replaced.",
        ]);
    }

    public function page()
    {
        $amenities = $this->amenityRepository->all();

        $currentLocationId = session('selected_location_id');
        $otherLocations = Location::where('is_active', true)
            ->when($currentLocationId, fn($q) => $q->where('id', '!=', $currentLocationId))
            ->orderBy('name')
            ->get();

        return view('admin.amenities', compact('amenities', 'otherLocations'));
    }
}
