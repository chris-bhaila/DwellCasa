<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingVerificationMail;
use App\Models\Booking;
use App\Models\Location;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function create(Location $location)
    {
        $roomTypes = RoomType::where('location_id', $location->id)
            ->where('is_active', true)
            ->get();
        return view('web.booking', compact('roomTypes', 'location'));
    }

    public function store(StoreBookingRequest $request, Location $location)
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

        $rate = $request->stay_type === 'short_term'
            ? $roomType->price_per_night
            : $roomType->price_per_month;

        $nights = (int) \Carbon\Carbon::parse($request->check_in_date)
            ->diffInDays($request->check_out_date);

        $total = $request->stay_type === 'short_term'
            ? $rate * $nights
            : $rate * ceil($nights / 30);

        // Store booking data in cache pending email verification.
        // The booking record is NOT written to the DB yet.
        $token = Str::uuid()->toString();

        $pending = [
            'validated'      => $request->validated(),
            'location_id'    => $location->id,
            'rate_per_night' => $request->stay_type === 'short_term' ? $rate : null,
            'rate_per_month' => $request->stay_type === 'long_term'  ? $rate : null,
            'total_amount'   => round($total, 2),
            'guest_email'    => $request->guest_email,
            'guest_name'     => $request->guest_name,
            'room_type_name' => $roomType->name,
            'booking_ref'    => $request->booking_ref,
            'check_in_date'  => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'num_guests'     => $request->num_guests,
        ];

        Cache::put("booking_verify:{$token}", $pending, now()->addMinutes(30));

        $verifyUrl = route('booking.verify', ['location' => $location->slug, 'token' => $token]);

        try {
            Mail::to($request->guest_email)->send(
                new BookingVerificationMail($verifyUrl, $pending, $location->name)
            );
        } catch (\Exception $e) {
            Cache::forget("booking_verify:{$token}");
            Log::warning('Booking verification email failed.', [
                'booking_ref' => $request->booking_ref,
                'email'       => $request->guest_email,
                'error'       => $e->getMessage(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['guest_email' => 'We could not send a verification email to this address. Please check the address and try again.']);
        }

        return redirect()->route('booking.create', $location)
            ->with('info', 'Almost there! We sent a verification link to ' . $request->guest_email . '. Please check your inbox and click the link to confirm your booking request.');
    }

    public function verify(Request $request, Location $location, string $token)
    {
        $pending = Cache::pull("booking_verify:{$token}");

        if (! $pending) {
            return redirect()->route('booking.create', $location)
                ->withErrors(['token' => 'This verification link has expired or already been used. Please submit a new booking request.']);
        }

        try {
            DB::transaction(function () use ($pending) {
                $roomType = RoomType::lockForUpdate()
                    ->findOrFail($pending['validated']['room_type_id']);

                $currentBookableRoomsCount = $roomType->rooms()
                    ->whereNotIn('status', ['maintenance', 'out_of_service'])
                    ->count();

                if ($currentBookableRoomsCount === 0) {
                    throw new \Exception('fully_booked:all');
                }

                $bookings = Booking::where('room_type_id', $pending['validated']['room_type_id'])
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where('check_in_date', '<', $pending['check_out_date'])
                    ->where('check_out_date', '>', $pending['check_in_date'])
                    ->get(['check_in_date', 'check_out_date']);

                $period = \Carbon\CarbonPeriod::create(
                    $pending['check_in_date'],
                    \Carbon\Carbon::parse($pending['check_out_date'])->subDay()
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

                $this->bookingRepository->create(array_merge($pending['validated'], [
                    'location_id'    => $pending['location_id'],
                    'status'         => 'pending',
                    'payment_status' => 'unpaid',
                    'rate_per_night' => $pending['rate_per_night'],
                    'rate_per_month' => $pending['rate_per_month'],
                    'total_amount'   => $pending['total_amount'],
                ]));
            });
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'fully_booked:')) {
                $date = str_replace('fully_booked:', '', $e->getMessage());
                $msg = $date === 'all'
                    ? 'Sorry, this room type is no longer available.'
                    : 'Sorry, this room type became fully booked on ' . $date . ' while your request was pending.';
                return redirect()->route('booking.create', $location)
                    ->withErrors(['room_type_id' => $msg]);
            }
            throw $e;
        }

        return redirect()->route('location.home', $location)
            ->with('success', 'Your booking request has been confirmed! Our team will review it and contact you shortly via email or phone.');
    }
}
