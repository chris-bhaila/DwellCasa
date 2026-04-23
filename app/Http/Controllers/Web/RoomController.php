<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Models\Booking;
use App\Contracts\WebsiteInfoRepositoryInterface;

class RoomController extends Controller
{
    protected $roomTypeRepository;
    protected $websiteInfoRepository;

    public function __construct(RoomTypeRepositoryInterface $roomTypeRepository,
        WebsiteInfoRepositoryInterface $websiteInfoRepository
    )
    {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->websiteInfoRepository = $websiteInfoRepository;
        
    }

    public function index()
    {
        $webInfo = $this->websiteInfoRepository->get();
        $roomTypes = $this->roomTypeRepository->all();
        return view('web.rooms', compact('roomTypes', 'webInfo'));
    }

    public function show($id)
    {
        $roomType = $this->roomTypeRepository->find($id);
        abort_if(!$roomType, 404);
        
        $totalRooms = \App\Models\Room::where('room_type_id', $id)
            ->whereNotIn('status', ['maintenance', 'out_of_service'])
            ->count();

        $bookedDates = [];
        if ($totalRooms === 0) {
            $period = \Carbon\CarbonPeriod::create(now(), now()->addYears(2));
            foreach ($period as $date) {
                $bookedDates[] = $date->format('Y-m-d');
            }
        } else {
            $bookings = Booking::where('room_type_id', $id)
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_out_date', '>', now()->toDateString())
                ->get(['check_in_date', 'check_out_date']);

            $dateCounts = [];
            foreach ($bookings as $booking) {
                // The checkout date is available for a new check-in, so we subtract a day for the period.
                $period = \Carbon\CarbonPeriod::create($booking->check_in_date, $booking->check_out_date->copy()->subDay());
                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $dateCounts[$dateStr] = ($dateCounts[$dateStr] ?? 0) + 1;
                }
            }

            foreach ($dateCounts as $date => $count) {
                if ($count >= $totalRooms) {
                    $bookedDates[] = $date;
                }
            }
            sort($bookedDates);
        }

        return view('web.room', compact('roomType', 'bookedDates'));
    }
}