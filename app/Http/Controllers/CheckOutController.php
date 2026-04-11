<?php

namespace App\Http\Controllers;

use App\Contracts\CheckOutRepositoryInterface;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    protected $checkOutRepository;

    public function __construct(CheckOutRepositoryInterface $checkOutRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
    }

    public function index(Request $request)
    {
        $checkOuts = $this->checkOutRepository->all();
        return response()->json([
            'data' => $checkOuts,
            'message' => 'Check-outs fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $checkOut = $this->checkOutRepository->find($id);
        return response()->json([
            'data' => $checkOut,
            'message' => 'Check-out fetched successfully'
        ], 200);
    }

    public function store(StoreCheckOutRequest $request)
    {
        $checkOut = DB::transaction(function () use ($request) {
            $checkOut = $this->checkOutRepository->create($request->validated());

            // Update booking status to checked_out
            $checkOut->booking->update(['status' => 'checked_out']);

            return $checkOut;
        });

        return response()->json([
            'success' => true,
            'message' => 'Check-out created successfully',
            'data' => $checkOut
        ], 201);
    }

    public function update(UpdateCheckOutRequest $request, $id)
    {
        $checkOut = $this->checkOutRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Check-out updated successfully',
            'data' => $checkOut
        ], 200);
    }

    public function destroy($id)
    {
        $this->checkOutRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Check-out deleted successfully'
        ], 200);
    }
}