<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\WebsiteInfoRepositoryInterface;


class AboutController extends Controller
{
    protected $websiteInfoRepository;

    public function __construct(
        WebsiteInfoRepositoryInterface $websiteInfoRepository

    ) {
        $this->websiteInfoRepository = $websiteInfoRepository;
    }
    public function index()
    {
        $webInfo = $this->websiteInfoRepository->get();

        return view('web.about', compact('webInfo'));
    }
}
