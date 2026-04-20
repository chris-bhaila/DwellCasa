<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\WebsiteInfoRepositoryInterface;


class ContactController extends Controller
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

        return view('web.contact', compact('webInfo'));
    }
}