<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Contracts\CheckOutRepositoryInterface;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use App\Models\Review;
use App\Mail\ReviewRequestMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckOutController extends Controller
{
    protected $checkOutRepository;

    public function __construct(CheckOutRepositoryInterface $checkOutRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
    }

    public function index()
    {
        $checkOuts = $this->checkOutRepository->all();
        return response()->json([
            'data' => $checkOuts,
            'message' => 'Check-outs fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $checkOut = $this->checkOutRepository->find($id);
        return response()->json([
            'data' => $checkOut,
            'message' => 'Check-out fetched successfully'
        ], 200);
    }

    public function store(StoreCheckOutRequest $request)
    {
        $emailData = null;

        try {
            $checkOut = DB::transaction(function () use ($request, &$emailData) {
                $validated = $request->validated();

                $checkOutRecord = $this->checkOutRepository->create($validated);

                $booking = Booking::with(['guest', 'roomType'])->findOrFail($validated['booking_id']);
                $booking->status = 'checked_out';
                $booking->checked_out_at = $validated['checked_out_at'];
                $booking->save();

                if ($booking->room_id) {
                    $room = Room::findOrFail($booking->room_id);
                    $room->status = 'available';
                    $room->save();
                }

                if ($booking->guest && $booking->room_type_id) {
                    $token = Str::uuid()->toString();

                    Review::create([
                        'name'         => $booking->guest->full_name,
                        'email'        => $booking->guest->email,
                        'booking_id'   => $booking->id,
                        'room_type_id' => $booking->room_type_id,
                        'guest_id'     => $booking->guest_id,
                        'type'         => 'room_type',
                        'status'       => 'pending',
                        'review_token' => $token,
                        'token_used'   => false,
                        'rating'       => 0,
                        'body'         => '',
                    ]);

                    // Capture for dispatch after the transaction commits
                    $emailData = ['booking' => $booking, 'token' => $token];
                }

                return $checkOutRecord;
            });

            // Queue the review request email after the transaction has committed
            if ($emailData) {
                try {
                    Mail::to($emailData['booking']->guest->email)
                        ->send(new ReviewRequestMail($emailData['booking'], $emailData['token']));
                } catch (\Exception $e) {
                    Log::error("Review request email failed: {$e->getMessage()}");
                }
            }

            $booking = Booking::with(['guest', 'room'])->findOrFail($request->input('booking_id'));

            activity()
                ->causedBy(auth()->user())
                ->performedOn($booking)
                ->withProperties(['location_id' => $booking->location_id])
                ->log("Checked out guest {$booking->guest->full_name} — Room {$booking->room->room_number} ({$booking->booking_ref})");

            return response()->json([
                'message' => 'Guest successfully checked out.',
                'data'    => $checkOut
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-out failed due to an internal error.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCheckOutRequest $request, $id)
    {
        $checkOut = $this->checkOutRepository->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Check-out updated successfully',
            'data' => $checkOut
        ], 200);
    }

    public function destroy($id)
    {
        $this->checkOutRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Check-out deleted successfully'
        ], 200);
    }
}
