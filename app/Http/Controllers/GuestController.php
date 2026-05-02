<?php

namespace App\Http\Controllers;

use App\Contracts\GuestRepositoryInterface;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Models\Guest;

class GuestController extends Controller
{
    protected $guestRepository;

    public function __construct(GuestRepositoryInterface $guestRepository)
    {
        $this->guestRepository = $guestRepository;
    }

    public function index()
    {
        $guests = $this->guestRepository->all();
        return response()->json([
            'data' => $guests,
            'message' => 'Guests fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $guest = $this->guestRepository->find($id);
        return response()->json([
            'data' => $guest,
            'message' => 'Guest fetched successfully'
        ], 200);
    }

    public function store(StoreGuestRequest $request)
    {
        $guest = $this->guestRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Guest created successfully',
            'data' => $guest
        ], 201);
    }

    public function update(UpdateGuestRequest $request, $id)
    {
        $guest = $this->guestRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Guest updated successfully',
            'data' => $guest
        ], 200);
    }

    public function destroy($id)
    {
        $this->guestRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Guest deleted successfully'
        ], 200);
    }

    public function trashed()
    {
        $guests = $this->guestRepository->trashed();
        return response()->json([
            'data'    => $guests,
            'message' => 'Trashed guests fetched successfully'
        ], 200);
    }

    public function restore($id)
    {
        $guest = $this->guestRepository->restore($id);
        return response()->json([
            'success' => true,
            'message' => 'Guest restored successfully',
            'data'    => $guest
        ], 200);
    }

    public function forceDelete($id)
    {
        $guest = Guest::onlyTrashed()->findOrFail($id);
        $this->guestRepository->forceDelete($id);
        return response()->json([
            'success' => true,
            'message' => 'Guest permanently deleted'
        ], 200);
    }
}