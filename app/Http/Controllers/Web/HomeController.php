<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Models\Booking;
use App\Models\Room;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $roomTypeRepository;
    protected $amenityRepository;
    protected $galleryImageRepository;
    protected $websiteInfoRepository;

    public function __construct(
        RoomTypeRepositoryInterface $roomTypeRepository,
        AmenityRepositoryInterface $amenityRepository,
        GalleryImageRepositoryInterface $galleryImageRepository,
        WebsiteInfoRepositoryInterface $websiteInfoRepository
    ) {
        $this->roomTypeRepository = $roomTypeRepository;
        $this->amenityRepository = $amenityRepository;
        $this->galleryImageRepository = $galleryImageRepository;
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function index()
    {
        $locations = \App\Models\Location::where('is_active', true)->orderBy('name')->get();
        $webInfo   = $this->websiteInfoRepository->getGlobal();
        $reviews   = \App\Models\Review::withoutGlobalScopes()
            ->where('status', 'approved')
            ->orderByDesc('rating')
            ->latest()
            ->take(8)
            ->get();

        return view('web.home', compact('locations', 'webInfo', 'reviews'));
    }

    public function location(\App\Models\Location $location)
    {
        $webInfo          = $this->websiteInfoRepository->getForLocation($location->id);
        $featuredRoomTypes = \App\Models\RoomType::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->take(3)
            ->get();
        $amenities        = \App\Models\Amenity::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->take(6)
            ->get();
        $galleryImages    = \App\Models\GalleryImage::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();
        $reviews          = \App\Models\Review::withoutGlobalScopes()
            ->where('location_id', $location->id)
            ->where('status', 'approved')
            ->orderByDesc('rating')
            ->latest()
            ->take(8)
            ->get();

        $bookedDatesByRoomType = [];
        foreach ($featuredRoomTypes as $rt) {
            $totalRooms = Room::withoutGlobalScopes()
                ->where('room_type_id', $rt->id)
                ->whereNotIn('status', ['maintenance', 'out_of_service'])
                ->count();

            if ($totalRooms === 0) {
                $period = CarbonPeriod::create(now(), now()->addYears(2));
                $bookedDatesByRoomType[$rt->id] = collect($period)->map(fn($d) => $d->format('Y-m-d'))->values()->toArray();
            } else {
                $bookings = Booking::where('room_type_id', $rt->id)
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where('check_out_date', '>', now()->toDateString())
                    ->get(['check_in_date', 'check_out_date']);

                $dateCounts = [];
                foreach ($bookings as $booking) {
                    $period = CarbonPeriod::create($booking->check_in_date, $booking->check_out_date->copy()->subDay());
                    foreach ($period as $date) {
                        $dateStr = $date->format('Y-m-d');
                        $dateCounts[$dateStr] = ($dateCounts[$dateStr] ?? 0) + 1;
                    }
                }

                $booked = [];
                foreach ($dateCounts as $date => $count) {
                    if ($count >= $totalRooms) {
                        $booked[] = $date;
                    }
                }
                sort($booked);
                $bookedDatesByRoomType[$rt->id] = $booked;
            }
        }

        return view('web.location', compact(
            'location',
            'webInfo',
            'featuredRoomTypes',
            'amenities',
            'galleryImages',
            'reviews',
            'bookedDatesByRoomType'
        ));
    }
}
