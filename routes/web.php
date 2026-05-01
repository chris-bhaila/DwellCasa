<?php

use App\Http\Controllers\MapController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\RoomController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

//Main website routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Location-based routes
Route::prefix('{location:slug}')
    ->where(['location' => '^(?!admin|api)[a-z0-9\-]+$'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'location'])->name('location.home');
        Route::get('/rooms', [RoomController::class, 'index'])->name('web.rooms.index');
        Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('web.rooms.show');
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
        Route::get('/about', [AboutController::class, 'index'])->name('about');
        Route::get('/contact', [ContactController::class, 'index'])->name('contact');
        Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    });

Route::get('/map', [MapController::class, 'getMapSettings'])->name('map.settings');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::post('/room-types/{id}/images', [RoomTypeController::class, 'uploadImage'])->name('room-types.images.upload');
Route::delete('/room-types/{id}/images/{imageId}', [RoomTypeController::class, 'deleteImage'])->name('room-types.images.delete');
Route::get('/hotel-review', function () {
    return view('web.hotel-review');
})->name('web.hotel-review');

Route::post('/hotel-review', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name'   => 'required|string|max:255',
        'email'  => 'required|email|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'body'   => 'required|string',
    ]);

    \App\Models\Review::create(array_merge($request->only(['name', 'email', 'rating', 'body']), [
        'type'   => 'hotel',
        'status' => 'pending',
    ]));

    return redirect()->route('home')->with('success', 'Thank you for your review! It will appear after approval.');
})->name('web.hotel-review.store');

Route::get('/review/{token}', function ($token) {
    $review = \App\Models\Review::where('review_token', $token)
        ->where('token_used', false)
        ->firstOrFail();
    return view('web.review', compact('review'));
})->name('review.form');

Route::post('/review/{token}', function (\Illuminate\Http\Request $request, $token) {
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'body'   => 'required|string',
    ]);

    $review = \App\Models\Review::where('review_token', $token)
        ->where('token_used', false)
        ->firstOrFail();

    $review->update([
        'rating'     => $request->rating,
        'body'       => $request->body,
        'token_used' => true,
    ]);

    return redirect()->route('home')->with('success', 'Thank you for your review!');
})->name('review.store');

// Admin Routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->prefix('admin')->group(function () {

    // ─── All authenticated users ──────────────────────────────────────
    Route::get('/', function (
        \App\Contracts\RoomRepositoryInterface $roomRepository,
        \App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository,
        \App\Contracts\CheckInRepositoryInterface $checkInRepository
    ) {
        $rooms = $roomRepository->all();
        $roomTypes = $roomTypeRepository->all();
        $checkIns = $checkInRepository->all();
        $bookings = \App\Models\Booking::with(['guest', 'roomType'])
            ->latest()
            ->take(5)
            ->get();
        return view('admin.home', compact('rooms', 'roomTypes', 'checkIns', 'bookings'));
    })->name('admin');

    Route::middleware('permission:view bookings')->group(function () {
        Route::get('/bookings/bookings', function (\Illuminate\Http\Request $request) {
            $filter = $request->query('filter', 'all');
            $query = \App\Models\Booking::with(['guest', 'roomType']);
            match ($filter) {
                'upcoming'  => $query->whereIn('status', ['pending', 'confirmed']),
                'inhouse'   => $query->where('status', 'checked_in'),
                'completed' => $query->whereIn('status', ['checked_out', 'cancelled']),
                default     => null
            };
            $query->orderByRaw("CASE WHEN status IN ('checked_out', 'cancelled') THEN 1 ELSE 0 END ASC");
            $bookings = $query->latest()->get();
            return view('admin.bookings.bookings', compact('bookings', 'filter'));
        })->name('admin.bookings');
    });

    Route::middleware('permission:create bookings')->group(function () {
        Route::get('/bookings/create', function (\App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
            $roomTypes = $roomTypeRepository->all();
            return view('admin.bookings.add-booking', compact('roomTypes'));
        })->name('admin.bookings.create');
    });

    Route::middleware('permission:edit bookings')->group(function () {
        Route::get('/bookings/{id}/edit', function ($id, \App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
            $booking = \App\Models\Booking::with('guest')->findOrFail($id);
            $roomTypes = $roomTypeRepository->all();
            $rooms = \App\Models\Room::where('room_type_id', $booking->room_type_id)
                ->where('status', 'available')
                ->whereDoesntHave('bookings', function ($query) use ($booking) {
                    $query->whereIn('status', ['confirmed', 'checked_in'])
                        ->where('id', '!=', $booking->id)
                        ->where('check_in_date', '<', $booking->check_out_date)
                        ->where('check_out_date', '>', $booking->check_in_date);
                })->orderBy('room_number')->get();
            $users = \App\Models\User::orderBy('name')->get();
            return view('admin.bookings.edit-booking', compact('booking', 'roomTypes', 'rooms', 'users'));
        })->name('admin.bookings.edit');
    });

    Route::middleware('permission:view inventory')->group(function () {
        Route::get('/inventory', function (\App\Contracts\InventoryRepositoryInterface $inventoryRepository) {
            $inventory = $inventoryRepository->all();
            return view('admin.inventory', compact('inventory'));
        })->name('admin.inventory');
    });

    Route::middleware('permission:manage inquiries')->group(function () {
        Route::get('/inquiry', function (\App\Contracts\InquiryRepositoryInterface $inquiryRepository) {
            $inquiries = $inquiryRepository->all();
            return view('admin.inquiry', compact('inquiries'));
        })->name('admin.inquiry');
    });

    // ─── Admin + Super Admin only ─────────────────────────────────────
    Route::middleware('permission:manage room types')->group(function () {
        Route::get('/room_type/index', function (\App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
            $roomTypes = $roomTypeRepository->all();
            $rooms = \App\Models\Room::with('roomType')->orderBy('room_number')->get();
            return view('admin.room_type.index', compact('roomTypes', 'rooms'));
        })->name('admin.room_type.index');

        Route::get('/room-types/create', function () {
            $amenities = \App\Models\Amenity::where('is_active', true)->get();
            return view('admin.room_type.create', compact('amenities'));
        })->name('admin.room_type.create');

        Route::get('/room-types/{id}', function ($id, \App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
            $roomType = $roomTypeRepository->find($id);
            abort_if(!$roomType, 404);
            $amenities = \App\Models\Amenity::where('is_active', true)->get();
            return view('admin.room_type.edit', compact('roomType', 'amenities'));
        })->name('admin.room_type.edit');
    });

    Route::middleware('permission:manage rooms')->group(function () {
        Route::get('/room_type/room/add-room', function (\App\Contracts\AmenityRepositoryInterface $amenityRepository) {
            $roomTypes = \App\Models\RoomType::withCount('rooms')->where('is_active', true)->orderBy('name')->get();
            $amenities = $amenityRepository->all();
            return view('admin.room_type.room.add-room', compact('roomTypes', 'amenities'));
        })->name('admin.room_type.room.add-room');

        Route::get('/room_type/room/{id}/edit', function ($id, \App\Contracts\AmenityRepositoryInterface $amenityRepository) {
            $room = \App\Models\Room::findOrFail($id);
            $amenities = $amenityRepository->all();
            $roomTypes = \App\Models\RoomType::withCount('rooms')->where('is_active', true)->orderBy('name')->get();
            return view('admin.room_type.room.edit-room', compact('room', 'roomTypes', 'amenities'));
        })->name('admin.room_type.room.edit');
    });

    Route::middleware('permission:manage amenities')->group(function () {
        Route::get('/amenities', function (\App\Contracts\AmenityRepositoryInterface $amenityRepository) {
            $amenities = $amenityRepository->all();
            return view('admin.amenities', compact('amenities'));
        })->name('admin.amenities');
    });

    Route::middleware('permission:manage gallery')->group(function () {
        Route::get('/gallery', function (\App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
            $roomTypes = $roomTypeRepository->all();
            $images = \App\Models\GalleryImage::latest()->get();
            return view('admin.gallery', compact('roomTypes', 'images'));
        })->name('admin.gallery');
    });

    Route::middleware('permission:manage website info')->group(function () {
        Route::get('/info', function () {
            return view('admin.info');
        })->name('admin.info');
    });

    Route::middleware('permission:manage reviews')->group(function () {
        Route::get('/reviews', function (\App\Contracts\ReviewRepositoryInterface $reviewRepository) {
            $reviews = $reviewRepository->all();
            return view('admin.reviews', compact('reviews'));
        })->name('admin.reviews');
    });

    // ─── Super Admin only ─────────────────────────────────────────────
    Route::middleware('permission:manage users')->group(function () {
        Route::get('/users', function () {
            $authUser = auth()->user();

            $users = \App\Models\User::with(['roles', 'permissions', 'location'])
                ->when(!$authUser->hasRole('super_admin'), function ($q) use ($authUser) {
                    $q->where('location_id', $authUser->location_id);
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin');
                })
                ->orderBy('name')
                ->get();

            $roles = \Spatie\Permission\Models\Role::with('permissions')
                ->when(!$authUser->hasRole('super_admin'), function ($q) {
                    $q->whereNotIn('name', ['super_admin', 'admin']);
                })
                ->get();

            $permissions = \Spatie\Permission\Models\Permission::all();

            return view('admin.users', compact('users', 'roles', 'permissions'));
        })->name('admin.users');
    });

    Route::middleware('permission:manage locations')->group(function () {
        Route::get('/locations', function () {
            $locations = \App\Models\Location::orderBy('name')->get();
            return view('admin.location', compact('locations'));
        })->name('admin.locations');
    });

    Route::middleware('permission:manage logs')->group(function () {
        Route::get('/activity-log', function () {
            $authUser = auth()->user();

            $logs = \Spatie\Activitylog\Models\Activity::with('causer')
                ->when(!$authUser->hasRole('super_admin'), function ($q) use ($authUser) {
                    $q->where('properties->location_id', $authUser->location_id);
                })
                ->latest()
                ->paginate(50);

            return view('admin.activity-log', compact('logs'));
        })->name('admin.activity-log');
    });

    Route::post('/switch-location', function (\Illuminate\Http\Request $request) {
        abort_if(!auth()->user()->hasRole('super_admin'), 403);

        $request->validate(['location_id' => 'nullable|exists:locations,id']);
        session(['selected_location_id' => $request->location_id]);

        return response()->json(['success' => true]);
    })->name('admin.switch-location');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    Route::put('/profile', function (\Illuminate\Http\Request $request) {
        abort_if(!auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Unauthorized action.');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore(auth()->id())],
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('status', 'profile-updated');
    })->name('admin.profile.update');

    Route::put('/password', function (\Illuminate\Http\Request $request) {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return back()->with('status', 'password-updated');
    })->name('password.update');
});
