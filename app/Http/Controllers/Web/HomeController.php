<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\GalleryImageRepositoryInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $roomTypeRepository;
    protected $amenityRepository;
    protected $galleryImageRepository;

    public function __construct(
        RoomTypeRepositoryInterface $roomTypeRepository,
        AmenityRepositoryInterface $amenityRepository,
        GalleryImageRepositoryInterface $galleryImageRepository
    ) {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->amenityRepository = $amenityRepository;
        $this->galleryImageRepository = $galleryImageRepository;
    }

    public function index()
    {
        $featuredRoomTypes = $this->roomTypeRepository->all()->take(3);
        $amenities = $this->amenityRepository->all()->take(6);
        $galleryImages = $this->galleryImageRepository->all()->take(8);

        return view('web.home', compact('featuredRoomTypes', 'amenities', 'galleryImages'));
    }
}