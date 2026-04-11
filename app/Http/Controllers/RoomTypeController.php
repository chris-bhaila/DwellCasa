<?php

namespace App\Http\Controllers;

use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use App\Models\GalleryImage;
use App\Models\Booking;
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

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
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

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ], 200);
    }

}
