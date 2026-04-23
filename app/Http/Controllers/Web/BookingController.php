<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\RoomType;
use Illuminate\Support\Facades\DB;

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
        $roomType = RoomType::findOrFail($request->room_type_id);
        $bookableRoomsCount = $roomType->rooms()
            ->whereNotIn('status', ['maintenance', 'out_of_service'])
            ->count();

        if ($bookableRoomsCount === 0) {
            return back()
                ->withInput()
                ->withErrors(['room_type_id' => 'Sorry, no rooms of this type are available for booking at the moment.']);
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
            : $rate * ceil($nights / 30);

        try {
            DB::transaction(function () use ($request, $rate, $total, $bookableRoomsCount) {
                // Lock room type row to prevent race conditions
                $roomType = RoomType::lockForUpdate()
                    ->findOrFail($request->room_type_id);

                // Re-check bookable rooms count inside transaction
                $currentBookableRoomsCount = $roomType->rooms()->whereNotIn('status', ['maintenance', 'out_of_service'])->count();
                if ($currentBookableRoomsCount === 0) {
                    throw new \Exception('fully_booked:all');
                }
                
                // Check availability inside the lock
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

                    if ($overlappingCount >= $currentBookableRoomsCount) {
                        throw new \Exception('fully_booked:' . $date->format('M j, Y'));
                    }
                }

                $this->bookingRepository->create(array_merge($request->validated(), [
                    'status'         => 'pending',
                    'payment_status' => 'unpaid',
                    'rate_per_night' => $request->stay_type === 'short_term' ? $rate : null,
                    'rate_per_month' => $request->stay_type === 'long_term' ? $rate : null,
                    'total_amount'   => round($total, 2),
                ]));
            });
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'fully_booked:')) {
                $date = str_replace('fully_booked:', '', $e->getMessage());
                if ($date === 'all') {
                    return back()
                        ->withInput()
                        ->withErrors(['room_type_id' => 'Sorry, this room type just became unavailable.']);
                }
                return back()
                    ->withInput()
                    ->withErrors(['room_type_id' => 'Sorry, this room type is fully booked on ' . $date . '.']);
            }
            throw $e;
        }

        return redirect()->route('home')
            ->with('success', 'Booking submitted successfully! We will contact you soon to confirm.');
    }
}
