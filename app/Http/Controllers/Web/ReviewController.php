<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class ReviewController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $token = $request->query('token');
        abort_if(!$token, 400, 'Missing review token.');

        session(['review_token_pending' => $token]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(\Illuminate\Http\Request $request)
    {
        $token = session()->pull('review_token_pending');

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            if (!$token) {
                return redirect()->route('home')
                    ->with('error', 'Authentication failed. Please try again.');
            }
            // Store token again so they can retry
            session(['review_token_pending' => $token]);
            return redirect()->route('review.form', $token)
                ->with('oauth_error', 'Google sign-in failed. Please try again.');
        }

        if (!$token) {
            return redirect()->route('home');
        }

        // Download avatar locally so it never expires
        try {
            $avatarUrl      = $googleUser->getAvatar();
            $avatarContents = file_get_contents($avatarUrl);
            $filename       = 'avatars/' . str_replace('-', '', (string) \Illuminate\Support\Str::uuid()) . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $avatarContents);
            session(['google_avatar' => $filename]);
        } catch (\Exception $e) {
            // Avatar download failed — store token and let them retry
            session(['review_token_pending' => $token]);
            return redirect()->route('review.form', $token)
                ->with('oauth_error', 'Could not retrieve your Google profile photo. Please try again.');
        }

        return redirect()->route('review.form', $token);
    }

    public function showTokenForm(string $token)
    {
        $review = Review::where('review_token', $token)
            ->where('token_used', false)
            ->firstOrFail();

        // If no avatar in session yet, gate with Google OAuth
        if (!session('google_avatar')) {
            session(['review_token_pending' => $token]);
            return Socialite::driver('google')->redirect();
        }

        $googleAvatar = session('google_avatar');

        return view('web.review', compact('review', 'googleAvatar', 'token'));
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

        $avatar = session()->pull('google_avatar');
        session()->forget('google_name');

        $review->update([
            'rating'     => $request->input('rating'),
            'body'       => $request->input('body'),
            'token_used' => true,
            'avatar'     => $avatar,
        ]);

        return redirect()->route('home')->with('success', 'Thank you for your review!');
    }
}
