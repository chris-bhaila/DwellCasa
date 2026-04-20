<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\WebsiteInfoRepositoryInterface;


class GalleryController extends Controller
{
    protected $galleryImageRepository;
    protected $websiteInfoRepository;


    public function __construct(GalleryImageRepositoryInterface $galleryImageRepository,
        WebsiteInfoRepositoryInterface $websiteInfoRepository
    )
    {
        $this->galleryImageRepository = $galleryImageRepository;
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function index()
    {
        $images = $this->galleryImageRepository->all();
        $webInfo = $this->websiteInfoRepository->get();

        return view('web.gallery', compact('images','webInfo'));
    }
}