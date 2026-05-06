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

    public function page(\Illuminate\Http\Request $request)
    {
        $filter = $request->query('filter', 'all');

        if ($filter === 'trashed') {
            $guests = \App\Models\Guest::onlyTrashed()
                ->withCount('bookings')
                ->with(['bookings' => fn($q) => $q->with('roomType')->latest()])
                ->latest('deleted_at')
                ->get();
            return view('admin.guests', compact('guests', 'filter') + ['guestJson' => collect()]);
        }

        $guests = \App\Models\Guest::withCount('bookings')
            ->with(['bookings' => fn($q) => $q->with('roomType')->latest()])
            ->latest()
            ->get();

        $guestJson = $guests->map(function ($g) {
            return [
                'id'        => $g->id,
                'full_name' => $g->full_name,
                'email'     => $g->email,
                'phone'     => $g->phone,
                'bookings'  => $g->bookings->map(function ($b) {
                    return [
                        'id'             => $b->id,
                        'booking_ref'    => $b->booking_ref,
                        'room_type_name' => $b->roomType?->name ?? 'N/A',
                        'check_in_date'  => $b->check_in_date?->format('M d, Y'),
                        'check_out_date' => $b->check_out_date?->format('M d, Y'),
                        'nights'         => ($b->check_in_date && $b->check_out_date)
                                            ? $b->check_in_date->diffInDays($b->check_out_date)
                                            : 0,
                        'status'         => $b->status,
                        'total_amount'   => $b->total_amount,
                        'amount_paid'    => $b->amount_paid,
                    ];
                })->values(),
            ];
        })->keyBy('id');

        return view('admin.guests', compact('guests', 'filter', 'guestJson'));
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
        Guest::onlyTrashed()->findOrFail($id);
        $this->guestRepository->forceDelete($id);
        return response()->json([
            'success' => true,
            'message' => 'Guest permanently deleted'
        ], 200);
    }
}