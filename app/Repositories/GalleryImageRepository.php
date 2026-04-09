<?php

namespace App\Repositories;

use App\Models\GalleryImage;
use App\Contracts\GalleryImageRepositoryInterface;

class GalleryImageRepository implements GalleryImageRepositoryInterface
{
    public function all()
    {
        return GalleryImage::all();
    }

    public function find($id)
    {
        return GalleryImage::findOrFail($id);
    }

    public function create(array $data)
    {
        return GalleryImage::create($data);
    }

    public function update($id, array $data)
    {
        $galleryImage = $this->find($id);
        $galleryImage->update($data);
        return $galleryImage;
    }

    public function delete($id)
    {
        $galleryImage = $this->find($id);
        $galleryImage->delete();
        return true;
    }
}