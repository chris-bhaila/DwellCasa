<?php

namespace App\Http\Controllers;

use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use App\Models\GalleryImage;
use App\Models\Booking;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = \Illuminate\Support\Str::slug($data['name']) . '_thumbnail.' . $file->getClientOriginalExtension();
            $data['thumbnail'] = $file->storeAs('room_types', $filename, 'public');
        }

        $roomType = $this->roomTypeRepository->create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                \App\Models\GalleryImage::create([
                    'filename'       => $path,
                    'category'       => 'rooms',
                    'imageable_type' => \App\Models\RoomType::class,
                    'imageable_id'   => $roomType->id,
                    'is_active'      => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully',
            'data' => $roomType
        ], 201);
    }

    public function update(UpdateRoomTypeRequest $request, $id)
    {
        $data = $request->validated();
        $roomType = $this->roomTypeRepository->find($id);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = $data['name'] ?? $roomType->name;
            $filename = \Illuminate\Support\Str::slug($name) . '_thumbnail.' . $file->getClientOriginalExtension();
            if ($roomType->thumbnail && $roomType->thumbnail !== 'room_types/' . $filename) {
                Storage::disk('public')->delete($roomType->thumbnail);
            }
            $data['thumbnail'] = $file->storeAs('room_types', $filename, 'public');
        }

        $updatedRoomType = $this->roomTypeRepository->update($id, $data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                \App\Models\GalleryImage::create([
                    'filename'       => $path,
                    'category'       => 'rooms',
                    'imageable_type' => \App\Models\RoomType::class,
                    'imageable_id'   => $roomType->id,
                    'is_active'      => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Room type updated successfully',
            'data' => $updatedRoomType
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
}
