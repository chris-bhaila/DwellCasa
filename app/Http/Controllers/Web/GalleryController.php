<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Models\Location;

class GalleryController extends Controller
{
    protected $galleryImageRepository;
    protected $websiteInfoRepository;


    public function __construct(
        GalleryImageRepositoryInterface $galleryImageRepository,
        WebsiteInfoRepositoryInterface $websiteInfoRepository
    ) {
        $this->galleryImageRepository = $galleryImageRepository;
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function index(Location $location)
    {
        $images = \App\Models\GalleryImage::where('location_id', $location->id)
            ->where('is_active', true)
            ->latest()
            ->get();
        $webInfo = $this->websiteInfoRepository->getForLocation($location->id);

        return view('web.gallery', compact('images', 'webInfo', 'location'));
    }
}
