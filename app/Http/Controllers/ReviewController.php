<?php

namespace App\Http\Controllers;

use App\Contracts\ReviewRepositoryInterface;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    protected $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index()
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

        $review = $this->reviewRepository->create([
            ...$request->validated(),
            'status'      => 'pending',
            'location_id' => $locationId,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($review)
            ->withProperties(['location_id' => $locationId])
            ->log("Created review by {$review->name}");

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

        $review = Review::where('review_token', $request->input('token'))->first();

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
            'rating'     => $request->input('rating'),
            'body'       => $request->input('body'),
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
        activity()
            ->causedBy(auth()->user())
            ->performedOn($review)
            ->withProperties(['location_id' => $review->location_id])
            ->log("Updated review by {$review->name}");
        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data'    => $review
        ], 200);
    }

    public function destroy($id)
    {
        $review = $this->reviewRepository->find($id);
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $review->location_id])
            ->log("Deleted review by {$review->name}");
        $this->reviewRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ], 200);
    }

    public function page()
    {
        $reviews = $this->reviewRepository->all();

        return view('admin.reviews', compact('reviews'));
    }

    public function showHotelReviewForm()
    {
        return view('web.hotel-review');
    }

    public function storeHotelReview(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'required|string',
        ]);

        Review::create(array_merge($request->only(['name', 'email', 'rating', 'body']), [
            'type'   => 'hotel',
            'status' => 'pending',
        ]));

        return redirect()->route('home')->with('success', 'Thank you for your review! It will appear after approval.');
    }

    public function showTokenForm(string $token)
    {
        $review = Review::where('review_token', $token)
            ->where('token_used', false)
            ->firstOrFail();

        return view('web.review', compact('review'));
    }

    public function storeTokenReview(Request $request, string $token)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'required|string',
        ]);

        $review = Review::where('review_token', $token)
            ->where('token_used', false)
            ->firstOrFail();

        $review->update([
            'rating'     => $request->input('rating'),
            'body'       => $request->input('body'),
            'token_used' => true,
        ]);

        return redirect()->route('home')->with('success', 'Thank you for your review!');
    }

    // Admin approve/reject
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $status = $request->input('status');
        $review = $this->reviewRepository->update($id, ['status' => $status]);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($review)
            ->withProperties(['location_id' => $review->location_id])
            ->log("Review by {$review->name} — status set to {$status}");
        return response()->json([
            'success' => true,
            'message' => 'Review status updated successfully',
            'data'    => $review
        ], 200);
    }
}
