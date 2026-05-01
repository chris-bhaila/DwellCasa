<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\WebsiteInfoRepositoryInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $roomTypeRepository;
    protected $amenityRepository;
    protected $galleryImageRepository;
    protected $websiteInfoRepository;

    public function __construct(
        RoomTypeRepositoryInterface $roomTypeRepository,
        AmenityRepositoryInterface $amenityRepository,
        GalleryImageRepositoryInterface $galleryImageRepository,
        WebsiteInfoRepositoryInterface $websiteInfoRepository
    ) {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->amenityRepository = $amenityRepository;
        $this->galleryImageRepository = $galleryImageRepository;
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function index()
    {
        $locations = \App\Models\Location::where('is_active', true)->orderBy('name')->get();
        $webInfo   = $this->websiteInfoRepository->getGlobal();
        $reviews   = \App\Models\Review::withoutGlobalScopes()
            ->where('status', 'approved')
            ->orderByDesc('rating')
            ->latest()
            ->take(8)
            ->get();

        return view('web.home', compact('locations', 'webInfo', 'reviews'));
    }

    public function location(\App\Models\Location $location)
    {
        $webInfo          = $this->websiteInfoRepository->getForLocation($location->id);
        $featuredRoomTypes = \App\Models\RoomType::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->take(3)
            ->get();
        $amenities        = \App\Models\Amenity::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->take(6)
            ->get();
        $galleryImages    = \App\Models\GalleryImage::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();
        $reviews          = \App\Models\Review::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('status', 'approved')
            ->orderByDesc('rating')
            ->latest()
            ->take(8)
            ->get();

        return view('web.location', compact(
            'location',
            'webInfo',
            'featuredRoomTypes',
            'amenities',
            'galleryImages',
            'reviews'
        ));
    }
}
