<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\RoomType;
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
        $this->bookingRepository  = $bookingRepository;
    }

    public function create()
    {
        $roomTypes = $this->roomTypeRepository->all();
        return view('web.booking', compact('roomTypes'));
    }

    public function store(StoreBookingRequest $request)
    {
        $roomType = RoomType::withCount('rooms')->findOrFail($request->room_type_id);

        if ($roomType->rooms_count === 0) {
            return back()
                ->withInput()
                ->withErrors(['room_type_id' => 'Sorry, no physical rooms have been added for this room type yet.']);
        }

        // Check availability
        $bookings = Booking::where('room_type_id', $request->room_type_id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $request->check_out_date)
            ->where('check_out_date', '>', $request->check_in_date)
            ->get(['check_in_date', 'check_out_date']);

        $period = \Carbon\CarbonPeriod::create(
            $request->check_in_date, 
            \Carbon\Carbon::parse($request->check_out_date)->subDay()
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $overlappingCount = $bookings->filter(function ($b) use ($dateStr) {
                return $dateStr >= $b->check_in_date->format('Y-m-d') 
                    && $dateStr < $b->check_out_date->format('Y-m-d');
            })->count();

            if ($overlappingCount >= $roomType->rooms_count) {
                return back()
                    ->withInput()
                    ->withErrors(['room_type_id' => 'Sorry, this room type is fully booked on ' . $date->format('M j, Y') . '.']);
            }
        }

        // Get rate from room type
        $rate = $request->stay_type === 'short_term'
            ? $roomType->price_per_night
            : $roomType->price_per_month;

        // Calculate total
        $nights = (int) \Carbon\Carbon::parse($request->check_in_date)
            ->diffInDays($request->check_out_date);

        $total = $request->stay_type === 'short_term'
            ? $rate * $nights
            : $rate * ($nights / 30);

        $this->bookingRepository->create(array_merge($request->validated(), [
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'rate_per_night' => $request->stay_type === 'short_term' ? $rate : null,
            'rate_per_month' => $request->stay_type === 'long_term' ? $rate : null,
            'total_amount'   => round($total, 2),
        ]));

        return redirect()->route('home')
            ->with('success', 'Booking submitted successfully! We will contact you soon to confirm.');
    }
}