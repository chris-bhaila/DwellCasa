<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreBookingInquiryRequest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $roomTypeRepository;

    public function __construct(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $this->roomTypeRepository = $roomTypeRepository;
    }

    public function create()
    {
        $roomTypes = $this->roomTypeRepository->all();
        return view('web.booking', compact('roomTypes'));
    }

    public function store(StoreBookingInquiryRequest $request)
    {
        // For now, just redirect with success
        // In real app, save to database using BookingInquiryRepository
        return redirect()->route('home')->with('success', 'Booking inquiry submitted successfully! We will contact you soon.');
    }
}