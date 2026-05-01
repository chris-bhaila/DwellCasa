<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Models\Location;
class ContactController extends Controller
{
    protected $websiteInfoRepository;

    public function __construct(
        WebsiteInfoRepositoryInterface $websiteInfoRepository

    ) {
        $this->websiteInfoRepository = $websiteInfoRepository;
    }
    public function index(Location $location)
    {
        $webInfo = $this->websiteInfoRepository->getForLocation($location->id);
        return view('web.contact', compact('webInfo', 'location'));
    }
}
