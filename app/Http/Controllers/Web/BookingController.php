<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $roomTypeRepository;
    protected $bookingRepository;

    public function __construct(
        RoomTypeRepositoryInterface $roomTypeRepository,
        BookingRepositoryInterface $bookingRepository
    ) {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function create()
    {
        $roomTypes = $this->roomTypeRepository->all();
        return view('web.booking', compact('roomTypes'));
    }

    public function store(StoreBookingRequest $request)
    {
        $this->bookingRepository->create($request->validated());

        return redirect()->route('home')->with('success', 'Booking submitted successfully! We will contact you soon to confirm.');
    }
}