<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Models\Location;
class AboutController extends Controller
{
    protected $websiteInfoRepository;

    public function __construct(WebsiteInfoRepositoryInterface $websiteInfoRepository)
    {
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function index(Location $location)
    {
        $webInfo = $this->websiteInfoRepository->getForLocation($location->id);
        return view('web.about', compact('webInfo', 'location'));
    }
}