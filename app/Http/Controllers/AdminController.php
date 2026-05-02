<?php

namespace App\Http\Controllers;

use App\Contracts\CheckInRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Models\Activity;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    protected function currentLocationId(): ?int
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return session('selected_location_id');
        }
        return $user->location_id;
    }

    public function dashboard(
        RoomRepositoryInterface $roomRepository,
        RoomTypeRepositoryInterface $roomTypeRepository,
        CheckInRepositoryInterface $checkInRepository
    ) {
        $rooms     = $roomRepository->all();
        $roomTypes = $roomTypeRepository->all();
        $checkIns  = $checkInRepository->all();
        $bookings  = Booking::with(['guest', 'roomType'])->latest()->take(5)->get();

        return view('admin.home', compact('rooms', 'roomTypes', 'checkIns', 'bookings'));
    }

    public function activityLogPage()
    {
        $authUser     = auth()->user();
        $isSuperAdmin = $authUser->hasRole('super_admin');
        $locationId   = $isSuperAdmin
            ? session('selected_location_id')
            : $authUser->location_id;

        $logs = Activity::with(['causer', 'location'])
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->latest()
            ->paginate(50);

        return view('admin.activity-log', compact('logs'));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403);

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
        ]);

        $user        = auth()->user();
        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user           = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('status', 'password-updated');
    }

    public function switchLocation(Request $request)
    {
        abort_if(!auth()->user()->hasRole('super_admin'), 403);

        $request->validate(['location_id' => 'nullable|exists:locations,id']);
        session(['selected_location_id' => $request->input('location_id')]);

        return response()->json(['success' => true]);
    }
}
