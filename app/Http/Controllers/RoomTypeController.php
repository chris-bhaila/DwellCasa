<?php

namespace App\Http\Controllers;

use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\GalleryImage;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\Activity;

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
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected. Please select a location before creating a room type.');

        $data = $request->validated();
        $data['location_id'] = $locationId;

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = \Illuminate\Support\Str::slug($data['name']) . '_thumbnail.' . $file->getClientOriginalExtension();
            $data['thumbnail'] = $file->storeAs('room_types', $filename, 'public');
        }

        $roomType = $this->roomTypeRepository->create($data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($roomType)
            ->withProperties(['location_id' => $locationId])
            ->log('Created room type ' . $roomType->name);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                \App\Models\GalleryImage::create([
                    'filename'       => $path,
                    'category'       => 'rooms',
                    'imageable_type' => \App\Models\RoomType::class,
                    'imageable_id'   => $roomType->id,
                    'is_active'      => true,
                    'location_id'    => $locationId,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully',
            'data'    => $roomType
        ], 201);
    }

    public function update(UpdateRoomTypeRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']); // never allow location reassignment

        $roomType = $this->roomTypeRepository->find($id);

        if (!empty($data['is_standalone'])) {
            $roomCount = Room::where('room_type_id', $roomType->id)->count();
            if ($roomCount > 1) {
                return response()->json([
                    'message' => 'Cannot set as standalone: this room type has ' . $roomCount . ' rooms. Remove all but one before marking it as standalone.',
                    'errors'  => [
                        'is_standalone' => [
                            'This room type has ' . $roomCount . ' rooms. Remove all but one before marking it as standalone.'
                        ]
                    ]
                ], 422);
            }
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = $data['name'] ?? $roomType->name;
            $filename = \Illuminate\Support\Str::slug($name) . '_thumbnail.' . $file->getClientOriginalExtension();
            if ($roomType->thumbnail && $roomType->thumbnail !== 'room_types/' . $filename) {
                Storage::disk('public')->delete($roomType->thumbnail);
            }
            $data['thumbnail'] = $file->storeAs('room_types', $filename, 'public');
        } else {
            unset($data['thumbnail']);
        }

        $updatedRoomType = $this->roomTypeRepository->update($id, $data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedRoomType)
            ->withProperties(['location_id' => $updatedRoomType->location_id])
            ->log('Updated room type ' . $updatedRoomType->name);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                \App\Models\GalleryImage::create([
                    'filename'       => $path,
                    'category'       => 'rooms',
                    'imageable_type' => \App\Models\RoomType::class,
                    'imageable_id'   => $roomType->id,
                    'is_active'      => true,
                    'location_id'    => $roomType->location_id, // inherit from existing record
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room type updated successfully',
            'data'    => $updatedRoomType
        ], 200);
    }

    public function destroy($id)
    {
        $roomType = $this->roomTypeRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $roomType->location_id])
            ->log('Deleted room type ' . $roomType->name);
        $this->roomTypeRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Room type deleted successfully'
        ], 200);
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:20480',
            'alt_text'   => 'nullable|string|max:255',
            'caption'    => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $roomType = $this->roomTypeRepository->find($id);

        $path = $request->file('image')->store('gallery', 'public');

        $image = GalleryImage::create([
            'filename'       => $path,
            'alt_text'       => $request->alt_text,
            'caption'        => $request->caption,
            'category'       => 'rooms',
            'imageable_type' => \App\Models\RoomType::class,
            'imageable_id'   => $roomType->id,
            'is_featured'    => $request->boolean('is_featured', false),
            'is_active'      => true,
            'sort_order'     => $request->sort_order ?? 0,
        ]);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($roomType)
            ->withProperties(['location_id' => $roomType->location_id])
            ->log('Uploaded image to room type ' . $roomType->name);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data'    => $image
        ], 201);
    }

    public function deleteImage($id, $imageId)
    {
        $roomType = $this->roomTypeRepository->find($id);

        $image = GalleryImage::where('id', $imageId)
            ->where('imageable_type', \App\Models\RoomType::class)
            ->where('imageable_id', $roomType->id)
            ->firstOrFail();

        if ($image->filename && Storage::disk('public')->exists($image->filename)) {
            Storage::disk('public')->delete($image->filename);
        }

        // Permanently delete the database record
        if (method_exists($image, 'forceDelete')) {
            $image->forceDelete();
        } else {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($roomType)
                ->withProperties(['location_id' => $roomType->location_id])
                ->log('Deleted image from room type ' . $roomType->name);
            $image->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ], 200);
    }

    public function availability(Request $request, $id)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $roomType = RoomType::withCount(['rooms' => function ($query) {
            $query->whereNotIn('status', ['maintenance', 'out_of_service']);
        }])->findOrFail($id);
        $totalRooms = $roomType->rooms_count;

        // Parse month range
        $startOfMonth = \Carbon\Carbon::parse($request->month)->startOfMonth();
        $endOfMonth   = \Carbon\Carbon::parse($request->month)->endOfMonth();

        // Get all bookings that overlap this month
        $bookings = Booking::where('room_type_id', $id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $endOfMonth)
            ->where('check_out_date', '>', $startOfMonth)
            ->get(['check_in_date', 'check_out_date']);

        // Find fully booked dates
        $fullyBookedDates = [];
        $current = $startOfMonth->copy();

        while ($current <= $endOfMonth) {
            $bookingsOnDate = $bookings->filter(function ($booking) use ($current) {
                return $current >= \Carbon\Carbon::parse($booking->check_in_date)
                    && $current < \Carbon\Carbon::parse($booking->check_out_date);
            })->count();

            if ($totalRooms === 0 || $bookingsOnDate >= $totalRooms) {
                $fullyBookedDates[] = $current->format('Y-m-d');
            }

            $current->addDay();
        }

        return response()->json([
            'data' => [
                'room_type_id'      => $id,
                'month'             => $request->month,
                'total_rooms'       => $totalRooms,
                'fully_booked_dates' => $fullyBookedDates,
            ],
            'message' => 'Availability fetched successfully'
        ]);
    }

    public function trashed()
    {
        $roomTypes = $this->roomTypeRepository->trashed();
        return response()->json([
            'data'    => $roomTypes,
            'message' => 'Trashed room types fetched successfully'
        ], 200);
    }

    public function restore($id)
    {
        $roomType = $this->roomTypeRepository->restore($id);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($roomType)
            ->withProperties(['location_id' => $roomType->location_id])
            ->log("Restored room type {$roomType->name}");
        return response()->json([
            'success' => true,
            'message' => 'Room type restored successfully',
            'data'    => $roomType
        ], 200);
    }

    public function forceDelete($id)
    {
        $roomType = RoomType::onlyTrashed()->findOrFail($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $roomType->location_id])
            ->log("Permanently deleted room type {$roomType->name}");
        $this->roomTypeRepository->forceDelete($id);
        return response()->json([
            'success' => true,
            'message' => 'Room type permanently deleted'
        ], 200);
    }

    public function page(Request $request)
    {
        $filter   = $request->query('filter', 'all');
        $rtFilter = $request->query('rt');

        if ($filter === 'trashed') {
            $roomTypes = RoomType::onlyTrashed()->latest('deleted_at')->get();
            $rooms     = Room::onlyTrashed()->with('roomType')->latest('deleted_at')->get();
        } else {
            $roomTypes = $this->roomTypeRepository->all();
            $rooms     = Room::with('roomType')
                ->when($rtFilter, fn($q) => $q->where('room_type_id', $rtFilter))
                ->orderBy('room_number')
                ->get();

            $roomIds = $rooms->pluck('id');

            $occupiedRoomIds = Booking::where('status', 'checked_in')
                ->whereNotNull('room_id')
                ->whereIn('room_id', $roomIds)
                ->pluck('room_id')
                ->all();

            $rooms->each(function ($room) use ($occupiedRoomIds) {
                if (in_array($room->id, $occupiedRoomIds)) {
                    $room->status = 'occupied';
                }
            });
        }

        return view('admin.room_type.index', compact('roomTypes', 'rooms', 'filter', 'rtFilter'));
    }

    public function createPage()
    {
        $amenities = Amenity::where('is_active', true)->get();

        return view('admin.room_type.create', compact('amenities'));
    }

    public function editPage(int $id)
    {
        $roomType = $this->roomTypeRepository->find($id);
        abort_if(!$roomType, 404);
        $amenities = Amenity::where('is_active', true)->get();

        return view('admin.room_type.edit', compact('roomType', 'amenities'));
    }
}
