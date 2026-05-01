<?php

namespace App\Http\Controllers;

use App\Contracts\BookingRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\Activity;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingRepository->all();
        return response()->json([
            'data' => $bookings,
            'message' => 'Bookings fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $booking = $this->bookingRepository->find($id);
        return response()->json([
            'data' => $booking,
            'message' => 'Booking fetched successfully'
        ], 200);
    }

    public function store(StoreBookingRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $status = $request->input('status', 'pending');

        if (in_array($status, ['pending', 'confirmed', 'checked_in'])) {
            $error = $this->checkAvailability($request->room_type_id, $request->check_in_date, $request->check_out_date);
            if ($error) {
                return response()->json(['message' => $error, 'errors' => ['room_type_id' => [$error]]], 422);
            }
        }

        $data = $request->validated();
        $data['location_id'] = $locationId;

        $booking = $this->bookingRepository->create($data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($booking)
            ->withProperties(['location_id' => $locationId])
            ->log('Created booking ' . $booking->booking_ref . ' for ' . ($booking->guest->full_name ?? $booking->guest_name));
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data'    => $booking
        ], 201);
    }

    public function update(UpdateBookingRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;
        $roomTypeId = $request->input('room_type_id', $booking->room_type_id);
        $checkIn    = $request->input('check_in_date', $booking->check_in_date);
        $checkOut   = $request->input('check_out_date', $booking->check_out_date);
        $status     = $request->input('status', $booking->status);

        if (in_array($status, ['pending', 'confirmed', 'checked_in'])) {
            $error = $this->checkAvailability($roomTypeId, $checkIn, $checkOut, $id);
            if ($error) {
                return response()->json(['message' => $error, 'errors' => ['room_type_id' => [$error]]], 422);
            }
        }

        $updatedBooking = $this->bookingRepository->update($id, $data);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedBooking)
            ->withProperties(['location_id' => $updatedBooking->location_id])
            ->log('Updated booking ' . $updatedBooking->booking_ref . ' — status: ' . $updatedBooking->status);

        if ($oldStatus !== 'confirmed' && $updatedBooking->status === 'confirmed') {
            try {
                Mail::to($updatedBooking->guest->email)
                    ->send(new BookingConfirmationMail($updatedBooking));
            } catch (\Exception $e) {
                \Log::error('Booking confirmation email failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data'    => $updatedBooking
        ], 200);
    }

    public function destroy($id)
    {
        $this->bookingRepository->delete($id);
        $booking = Booking::findOrFail($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $booking->location_id])
            ->log('Deleted booking ' . $booking->booking_ref);
        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ], 200);
    }

    private function checkAvailability($roomTypeId, $checkIn, $checkOut, $excludeBookingId = null)
    {
        $roomType = RoomType::withCount(['rooms' => function ($query) {
            $query->whereNotIn('status', ['maintenance', 'out_of_service']);
        }])->findOrFail($roomTypeId);

        if ($roomType->rooms_count === 0) {
            return 'Sorry, no physical rooms have been added for this room type yet.';
        }

        $query = Booking::where('room_type_id', $roomTypeId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $bookings = $query->get(['check_in_date', 'check_out_date']);
        $period = CarbonPeriod::create($checkIn, Carbon::parse($checkOut)->subDay());

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $overlappingCount = $bookings->filter(function ($b) use ($dateStr) {
                return $dateStr >= Carbon::parse($b->check_in_date)->format('Y-m-d')
                    && $dateStr < Carbon::parse($b->check_out_date)->format('Y-m-d');
            })->count();

            if ($overlappingCount >= $roomType->rooms_count) {
                return 'Sorry, this room type is fully booked on ' . $date->format('M j, Y') . '.';
            }
        }

        return null;
    }
}
