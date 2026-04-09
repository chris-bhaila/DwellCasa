<?php

namespace App\Http\Controllers;

use App\Contracts\ServiceRepositoryInterface;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;

class ServiceController extends Controller
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index()
    {
        $services = $this->serviceRepository->all();
        return response()->json(['data' => $services, 'message' => 'Services fetched successfully'], 200);
    }

    public function show($id)
    {
        $service = $this->serviceRepository->find($id);
        return response()->json(['data' => $service, 'message' => 'Service fetched successfully'], 200);
    }

    public function store(StoreServiceRequest $request)
    {
        $service = $this->serviceRepository->create($request->validated());
        return response()->json(['success' => true, 'message' => 'Service created successfully', 'data' => $service], 201);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $service = $this->serviceRepository->update($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Service updated successfully', 'data' => $service], 200);
    }

    public function destroy($id)
    {
        $this->serviceRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'Service deleted successfully'], 200);
    }
}