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
        $webInfo = $this->websiteInfoRepository->get();
        $featuredRoomTypes = $this->roomTypeRepository->all()->take(3);
        $amenities = $this->amenityRepository->all()->take(6);
        $galleryImages = $this->galleryImageRepository->all()->take(8);

        return view('web.home', compact('featuredRoomTypes', 'amenities', 'galleryImages', 'webInfo'));
    }
}