<?php

namespace App\Http\Controllers;

use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Http\Requests\UpdateWebsiteInfoRequest;
use Illuminate\Http\Request;

class WebsiteInfoController extends Controller
{
    protected $websiteInfoRepository;

    public function __construct(WebsiteInfoRepositoryInterface $websiteInfoRepository)
    {
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function show()
    {
        $info = $this->websiteInfoRepository->get();
        return response()->json([
            'data'    => $info,
            'message' => 'Website info fetched successfully'
        ], 200);
    }

    public function update(UpdateWebsiteInfoRequest $request)
    {
        $info = $this->websiteInfoRepository->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Website info updated successfully',
            'data'    => $info
        ], 200);
    }
}