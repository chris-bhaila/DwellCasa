<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);

        $loginCredentials = [
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($loginCredentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Super admin — store selected location in session
            if ($user->hasRole('super_admin')) {
                $defaultLocation = $request->input('location_id')
                    ?? Location::orderBy('id')->value('id');
                session(['selected_location_id' => $defaultLocation]);
                return redirect()->intended(route('admin'));
            }

            // Admin/staff — must belong to a location
            if (!$user->location_id) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not assigned to any location. Please contact your administrator.',
                ]);
            }

            // Store location in session for consistency
            session(['selected_location_id' => $user->location_id]);

            return redirect()->intended(route('admin'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
