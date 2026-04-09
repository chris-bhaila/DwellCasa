<?php

namespace App\Http\Controllers;

use App\Contracts\GalleryImageRepositoryInterface;
use App\Http\Requests\StoreGalleryImageRequest;
use App\Http\Requests\UpdateGalleryImageRequest;

class GalleryImageController extends Controller
{
    protected $galleryImageRepository;

    public function __construct(GalleryImageRepositoryInterface $galleryImageRepository)
    {
        $this->galleryImageRepository = $galleryImageRepository;
    }

    public function index()
    {
        $images = $this->galleryImageRepository->all();
        return response()->json(['data' => $images, 'message' => 'Gallery images fetched successfully'], 200);
    }

    public function show($id)
    {
        $image = $this->galleryImageRepository->find($id);
        return response()->json(['data' => $image, 'message' => 'Gallery image fetched successfully'], 200);
    }

    public function store(StoreGalleryImageRequest $request)
    {
        $image = $this->galleryImageRepository->create($request->validated());
        return response()->json(['success' => true, 'message' => 'Gallery image created successfully', 'data' => $image], 201);
    }

    public function update(UpdateGalleryImageRequest $request, $id)
    {
        $image = $this->galleryImageRepository->update($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Gallery image updated successfully', 'data' => $image], 200);
    }

    public function destroy($id)
    {
        $this->galleryImageRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Gallery image deleted successfully'], 200);
    }
}
