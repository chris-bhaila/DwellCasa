<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Contracts\CheckOutRepositoryInterface;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Mail\ReviewRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckOutController extends Controller
{
    protected $checkOutRepository;

    public function __construct(CheckOutRepositoryInterface $checkOutRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
    }

    public function index(Request $request)
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
        try {
            $checkOut = DB::transaction(function () use ($request) {
                $validated = $request->validated();
    
                // 1. Create check_out record
                $checkOutRecord = $this->checkOutRepository->create($validated);
    
                // 2. Update booking status
                $booking = Booking::with(['guest', 'roomType'])->findOrFail($validated['booking_id']);
                $booking->status = 'checked_out';
                $booking->checked_out_at = $validated['checked_out_at'];
                $booking->save();
    
                // 3. Update room status back to available
                if ($booking->room_id) {
                    $room = Room::findOrFail($booking->room_id);
                    $room->status = 'available';
                    $room->save();
                }
    
                // 4. Generate review token and create pending review record
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
                        'rating'       => 0, // placeholder until guest submits
                        'body'         => '', // placeholder until guest submits
                    ]);
    
                    // 5. Send review request email
                    try {
                        Mail::to($booking->guest->email)
                            ->send(new ReviewRequestMail($booking, $token));
                    } catch (\Exception $e) {
                        \Log::error('Review request email failed: ' . $e->getMessage());
                    }
                }
    
                return $checkOutRecord;
            });
    
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
