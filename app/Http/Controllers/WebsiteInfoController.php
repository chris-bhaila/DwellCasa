<?php

namespace App\Http\Controllers;

use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Http\Requests\UpdateWebsiteInfoRequest;
use Illuminate\Http\Request;

class WebsiteInfoController extends Controller
{
    protected WebsiteInfoRepositoryInterface $websiteInfoRepository;

    public function __construct(WebsiteInfoRepositoryInterface $websiteInfoRepository)
    {
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function show(Request $request)
    {
        $locationId = $request->query('location_id');
        $info = $this->websiteInfoRepository->getForLocation($locationId);
        return response()->json([
            'data'    => $info,
            'message' => 'Website info fetched successfully'
        ], 200);
    }

    public function update(UpdateWebsiteInfoRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $data = $request->validated();
        unset($data['location_id']); // never from the form

        $imageFields = ['homepage_main_image', 'homepage_end_image', 'about_image'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('website', 'public');
            }
        }

        $info = $this->websiteInfoRepository->updateOrCreateForLocation($locationId, $data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($info)
            ->withProperties(['location_id' => $locationId])
            ->log('Updated website info');
            
        return response()->json([
            'success' => true,
            'message' => 'Website info updated successfully',
            'data'    => $info
        ], 200);
    }

    public function page()
    {
        return view('admin.info');
    }

    public function pageGlobal()
    {
        $info = $this->websiteInfoRepository->getGlobal();
        return view('admin.home-info', compact('info'));
    }

    public function updateGlobal(UpdateWebsiteInfoRequest $request)
    {
        $data = $request->validated();

        $imageFields = ['homepage_main_image', 'homepage_end_image'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('website', 'public');
            }
        }

        $info = $this->websiteInfoRepository->updateOrCreateGlobal($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($info)
            ->log('Updated global home page info');

        return response()->json([
            'success' => true,
            'message' => 'Home page info updated successfully',
            'data'    => $info,
        ]);
    }
}
