<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Models\Booking;

class RoomController extends Controller
{
    protected $roomTypeRepository;

    public function __construct(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $this->roomTypeRepository = $roomTypeRepository;
    }

    public function index()
    {
        $roomTypes = $this->roomTypeRepository->all();
        return view('web.rooms', compact('roomTypes'));
    }

    public function show($id)
    {
        $roomType = $this->roomTypeRepository->find($id);
        abort_if(!$roomType, 404);
        
        $bookedDates = Booking::where('room_type_id', $id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get(['check_in_date', 'check_out_date'])
            ->map(fn($b) => [
                'from' => $b->check_in_date->format('Y-m-d'),
                'to'   => $b->check_out_date->format('Y-m-d'),
            ]);

        return view('web.room', compact('roomType', 'bookedDates'));
    }
}