<?php

namespace App\Http\Controllers;

use App\Contracts\PropertySettingRepositoryInterface;
use App\Http\Requests\StorePropertySettingRequest;
use App\Http\Requests\UpdatePropertySettingRequest;

class PropertySettingController extends Controller
{
    protected $propertySettingRepository;

    public function __construct(PropertySettingRepositoryInterface $propertySettingRepository)
    {
        $this->propertySettingRepository = $propertySettingRepository;
    }

    public function index()
    {
        $settings = $this->propertySettingRepository->all();
        return response()->json([
            'data' => $settings,
            'message' => 'Property settings fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $setting = $this->propertySettingRepository->find($id);
        return response()->json([
            'data' => $setting,
            'message' => 'Property setting fetched successfully'
        ], 200);
    }

    public function store(StorePropertySettingRequest $request)
    {
        $setting = $this->propertySettingRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Property setting created successfully',
            'data' => $setting
        ], 201);
    }

    public function update(UpdatePropertySettingRequest $request, $id)
    {
        $setting = $this->propertySettingRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Property setting updated successfully',
            'data' => $setting
        ], 200);
    }

    public function destroy($id)
    {
        $this->propertySettingRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Property setting deleted successfully'
        ], 200);
    }
}