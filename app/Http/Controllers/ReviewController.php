<?php

namespace App\Http\Controllers;

use App\Contracts\ReviewRepositoryInterface;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;
use App\Models\Review;
use Spatie\Activitylog\Facades\Activity;

class ReviewController extends Controller
{
    protected $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index(Request $request)
    {
        $reviews = $this->reviewRepository->all();
        return response()->json([
            'data'    => $reviews,
            'message' => 'Reviews fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $review = $this->reviewRepository->find($id);
        return response()->json([
            'data'    => $review,
            'message' => 'Review fetched successfully'
        ], 200);
    }

    public function store(StoreReviewRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $review = $this->reviewRepository->create(array_merge($request->validated(), [
            'status'      => 'pending',
            'location_id' => $locationId,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully. It will appear after approval.',
            'data'    => $review
        ], 201);
    }

    // For verified guest reviews submitted via token link
    public function storeVerified(Request $request)
    {
        $request->validate([
            'token'  => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'required|string',
        ]);

        $review = Review::where('review_token', $request->token)->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired review token.'
            ], 404);
        }

        if ($review->token_used) {
            return response()->json([
                'success' => false,
                'message' => 'This review link has already been used.'
            ], 400);
        }

        $this->reviewRepository->update($review->id, [
            'rating'     => $request->rating,
            'body'       => $request->body,
            'status'     => 'pending',
            'token_used' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review! It will appear after approval.',
        ], 200);
    }

    public function update(UpdateReviewRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']);

        $review = $this->reviewRepository->update($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data'    => $review
        ], 200);
    }

    public function destroy($id)
    {
        $this->reviewRepository->delete($id);
        $review = $this->reviewRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $review->location_id])
            ->log('Deleted review by ' . $review->name);
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ], 200);
    }

    // Admin approve/reject
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $review = $this->reviewRepository->update($id, ['status' => $request->status]);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($review)
            ->withProperties(['location_id' => $review->location_id])
            ->log('Review by ' . $review->name . ' — status set to ' . $request->status);
        return response()->json([
            'success' => true,
            'message' => 'Review status updated successfully',
            'data'    => $review
        ], 200);
    }
}
