<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\GalleryImageRepositoryInterface;

class GalleryController extends Controller
{
    protected $galleryImageRepository;

    public function __construct(GalleryImageRepositoryInterface $galleryImageRepository)
    {
        $this->galleryImageRepository = $galleryImageRepository;
    }

    public function index()
    {
        $images = $this->galleryImageRepository->all();
        return view('web.gallery', compact('images'));
    }
}