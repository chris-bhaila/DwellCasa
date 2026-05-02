<?php

namespace App\Http\Controllers;

use App\Contracts\BookingRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index()
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
        $data = $request->validated();
        $data['location_id'] = $locationId;

        try {
            $booking = DB::transaction(function () use ($request, $status, $data) {
                if (\in_array($status, ['pending', 'confirmed', 'checked_in'])) {
                    // Lock the room type row to prevent concurrent bookings from slipping through
                    RoomType::lockForUpdate()->findOrFail($request->input('room_type_id'));

                    $error = $this->checkAvailability(
                        $request->input('room_type_id'),
                        $request->input('check_in_date'),
                        $request->input('check_out_date')
                    );
                    if ($error) {
                        throw new \Exception("availability:{$error}");
                    }
                }
                return $this->bookingRepository->create($data);
            });
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'availability:')) {
                $msg = \substr($e->getMessage(), \strlen('availability:'));
                return response()->json(['message' => $msg, 'errors' => ['room_type_id' => [$msg]]], 422);
            }
            throw $e;
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($booking)
            ->withProperties(['location_id' => $locationId])
            ->log("Created booking {$booking->booking_ref} for " . ($booking->guest->full_name ?? $booking->guest_name));

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
        $oldStatus  = $booking->status;
        $roomTypeId = $request->input('room_type_id', $booking->room_type_id);
        $checkIn    = $request->input('check_in_date', $booking->check_in_date);
        $checkOut   = $request->input('check_out_date', $booking->check_out_date);
        $status     = $request->input('status', $booking->status);

        try {
            $updatedBooking = DB::transaction(function () use ($id, $data, $status, $roomTypeId, $checkIn, $checkOut) {
                if (\in_array($status, ['pending', 'confirmed', 'checked_in'])) {
                    // Lock the room type row to prevent concurrent bookings from slipping through
                    RoomType::lockForUpdate()->findOrFail($roomTypeId);

                    $error = $this->checkAvailability($roomTypeId, $checkIn, $checkOut, $id);
                    if ($error) {
                        throw new \Exception("availability:{$error}");
                    }
                }
                return $this->bookingRepository->update($id, $data);
            });
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'availability:')) {
                $msg = \substr($e->getMessage(), \strlen('availability:'));
                return response()->json(['message' => $msg, 'errors' => ['room_type_id' => [$msg]]], 422);
            }
            throw $e;
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedBooking)
            ->withProperties(['location_id' => $updatedBooking->location_id])
            ->log("Updated booking {$updatedBooking->booking_ref} — status: {$updatedBooking->status}");

        if ($oldStatus !== 'confirmed' && $updatedBooking->status === 'confirmed') {
            try {
                Mail::to($updatedBooking->guest->email)
                    ->queue(new BookingConfirmationMail($updatedBooking));
            } catch (\Exception $e) {
                Log::error("Booking confirmation email failed: {$e->getMessage()}");
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
        $booking = $this->bookingRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $booking->location_id])
            ->log("Deleted booking {$booking->booking_ref}");
        $this->bookingRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ], 200);
    }

    public function trashed()
    {
        $bookings = $this->bookingRepository->trashed();
        return response()->json([
            'data'    => $bookings,
            'message' => 'Trashed bookings fetched successfully'
        ], 200);
    }

    public function restore($id)
    {
        $booking = $this->bookingRepository->restore($id);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($booking)
            ->withProperties(['location_id' => $booking->location_id])
            ->log("Restored booking {$booking->booking_ref}");
        return response()->json([
            'success' => true,
            'message' => 'Booking restored successfully',
            'data'    => $booking
        ], 200);
    }

    public function forceDelete($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $booking->location_id])
            ->log("Permanently deleted booking {$booking->booking_ref}");
        $this->bookingRepository->forceDelete($id);
        return response()->json([
            'success' => true,
            'message' => 'Booking permanently deleted'
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
            $overlappingCount = $bookings->filter(
                fn($b) => $dateStr >= Carbon::parse($b->check_in_date)->format('Y-m-d')
                    && $dateStr < Carbon::parse($b->check_out_date)->format('Y-m-d')
            )->count();

            if ($overlappingCount >= $roomType->rooms_count) {
                return "Sorry, this room type is fully booked on {$date->format('M j, Y')}.";
            }
        }

        return null;
    }

    public function page(\Illuminate\Http\Request $request)
    {
        $filter = $request->query('filter', 'all');

        if ($filter === 'trashed') {
            $bookings = Booking::onlyTrashed()
                ->with(['guest', 'roomType'])
                ->latest('deleted_at')
                ->get();

            return view('admin.bookings.bookings', compact('bookings', 'filter'));
        }

        $query = Booking::with(['guest', 'roomType']);
        match ($filter) {
            'upcoming'  => $query->whereIn('status', ['pending', 'confirmed']),
            'inhouse'   => $query->where('status', 'checked_in'),
            'completed' => $query->whereIn('status', ['checked_out', 'cancelled']),
            default     => null,
        };
        $query->orderByRaw("CASE WHEN status IN ('checked_out', 'cancelled') THEN 1 ELSE 0 END ASC");
        $bookings = $query->latest()->get();

        return view('admin.bookings.bookings', compact('bookings', 'filter'));
    }

    public function createPage(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $roomTypes = $roomTypeRepository->all();

        return view('admin.bookings.add-booking', compact('roomTypes'));
    }

    public function editPage(int $id, RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $booking   = Booking::with('guest')->findOrFail($id);
        $roomTypes = $roomTypeRepository->all();
        $rooms     = Room::where('room_type_id', $booking->room_type_id)
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($booking) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                    ->where('id', '!=', $booking->id)
                    ->where('check_in_date', '<', $booking->check_out_date)
                    ->where('check_out_date', '>', $booking->check_in_date);
            })->orderBy('room_number')->get();
        $users = User::orderBy('name')->get();

        return view('admin.bookings.edit-booking', compact('booking', 'roomTypes', 'rooms', 'users'));
    }
}
