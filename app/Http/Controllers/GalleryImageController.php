<?php

namespace App\Http\Controllers;

use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreGalleryImageRequest;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
class GalleryImageController extends Controller
{
    /**
     * Store a newly created gallery image.
     *
     * @param  \App\Http\Requests\StoreGalleryImageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreGalleryImageRequest $request): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $validated = $request->validated();
        $validated['location_id'] = $locationId;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');
            $validated['filename'] = $path;
        }

        unset($validated['image']);

        $image = GalleryImage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => $image,
        ], 201);
    }

    /**
     * Remove the specified gallery image from storage.
     *
     * @param  \App\Models\GalleryImage  $galleryImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(GalleryImage $galleryImage): JsonResponse
    {
        // Delete the file from storage if it exists
        if ($galleryImage->filename && Storage::disk('public')->exists($galleryImage->filename)) {
            Storage::disk('public')->delete($galleryImage->filename);
        }

        // Permanently delete the database record
        if (method_exists($galleryImage, 'forceDelete')) {
            $galleryImage->forceDelete();
        } else {
            $galleryImage->delete();
        }

        return response()->json(['success' => true, 'message' => 'Image deleted successfully.'], 200);
    }

    public function page(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $roomTypes = $roomTypeRepository->all();
        $images    = GalleryImage::latest()->get();

        return view('admin.gallery', compact('roomTypes', 'images'));
    }
}
