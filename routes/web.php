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
Route::get('/rooms', [RoomController::class, 'index'])->name('web.rooms.index');
Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('web.rooms.show');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/map', [MapController::class, 'getMapSettings'])->name('map.settings');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::post('/room-types/{id}/images', [RoomTypeController::class, 'uploadImage'])->name('room-types.images.upload');
Route::delete('/room-types/{id}/images/{imageId}', [RoomTypeController::class, 'deleteImage'])->name('room-types.images.delete');

//Admin Routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.home');
    })->name('admin');

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

    Route::get('/room_type/room/add-room', function (\App\Contracts\AmenityRepositoryInterface $amenityRepository) {
        $roomTypes = \App\Models\RoomType::where('is_active', true)->orderBy('name')->get();
        $amenities = $amenityRepository->all();
        return view('admin.room_type.room.add-room', compact('roomTypes', 'amenities'));
    })->name('admin.room_type.room.add-room');

    Route::get('/room_type/room/{id}/edit', function ($id, \App\Contracts\AmenityRepositoryInterface $amenityRepository) {
        $room = \App\Models\Room::findOrFail($id);
        $amenities = $amenityRepository->all();
        $roomTypes = \App\Models\RoomType::where('is_active', true)->orderBy('name')->get();
        return view('admin.room_type.room.edit-room', compact('room', 'roomTypes','amenities'));
    })->name('admin.room_type.room.edit');

    Route::get('/bookings/{id}/edit', function ($id, \App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
        $booking = \App\Models\Booking::with('guest')->findOrFail($id);
        $roomTypes = $roomTypeRepository->all();
        return view('admin.bookings.edit-booking', compact('booking', 'roomTypes'));
    })->name('admin.bookings.edit');

    Route::get('/bookings/bookings', function (\App\Contracts\BookingRepositoryInterface $bookingRepository, \App\Contracts\RoomTypeRepositoryInterface $roomTypeRepository) {
        $bookings = $bookingRepository->all();
        $roomTypes = $roomTypeRepository->all();
        return view('admin.bookings.bookings', compact('bookings', 'roomTypes'));
    })->name('admin.bookings');

    Route::get('/amenities', function (\App\Contracts\AmenityRepositoryInterface $amenityRepository) {
        $amenities = $amenityRepository->all();
        return view('admin.amenities', compact('amenities'));
    });

    Route::get('/gallery', function () {
        return view('admin.gallery');
    });

    Route::get('/inquiries', function () {
        return view('admin.inquiries');
    });
});