<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteInfoController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\BookingController as WebBookingController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\RoomController as WebRoomController;
use App\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

// ─── Public website ───────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('{location:slug}')
    ->where(['location' => '^(?!admin|api)[a-z0-9\-]+$'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'location'])->name('location.home');
        Route::get('/rooms', [WebRoomController::class, 'index'])->name('web.rooms.index');
        Route::get('/rooms/{id}', [WebRoomController::class, 'show'])->name('web.rooms.show');
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
        Route::get('/about', [AboutController::class, 'index'])->name('about');
        Route::get('/contact', [ContactController::class, 'index'])->name('contact');
        Route::get('/booking', [WebBookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [WebBookingController::class, 'store'])->name('booking.store');
    });

Route::get('/map', [MapController::class, 'getMapSettings'])->name('map.settings');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::post('/room-types/{id}/images', [RoomTypeController::class, 'uploadImage'])->name('room-types.images.upload');
Route::delete('/room-types/{id}/images/{imageId}', [RoomTypeController::class, 'deleteImage'])->name('room-types.images.delete');

Route::get('/hotel-review', [ReviewController::class, 'showHotelReviewForm'])->name('web.hotel-review');
Route::post('/hotel-review', [ReviewController::class, 'storeHotelReview'])->name('web.hotel-review.store');
Route::get('/review/{token}', [ReviewController::class, 'showTokenForm'])->name('review.form');
Route::post('/review/{token}', [ReviewController::class, 'storeTokenReview'])->name('review.store');

// ─── Admin auth ───────────────────────────────────────────────────────────────

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ─── Admin (authenticated) ────────────────────────────────────────────────────

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('admin');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    Route::middleware('permission:view bookings')->group(function () {
        Route::get('/bookings/bookings', [BookingController::class, 'page'])->name('admin.bookings');
    });

    Route::middleware('permission:view revenue')->group(function () {
        Route::get('/revenue', [AdminController::class, 'revenue'])->name('admin.revenue');
    });

    Route::middleware('permission:manage guests')->group(function () {
        Route::get('/guests', [GuestController::class, 'page'])->name('admin.guests');
    });

    Route::middleware('permission:create bookings')->group(function () {
        Route::get('/bookings/create', [BookingController::class, 'createPage'])->name('admin.bookings.create');
    });

    Route::middleware('permission:edit bookings')->group(function () {
        Route::get('/bookings/{id}/edit', [BookingController::class, 'editPage'])->name('admin.bookings.edit');
    });

    Route::middleware('permission:view inventory')->group(function () {
        Route::get('/inventory', [InventoryItemController::class, 'inventoryDashboard'])
            ->name('admin.inventory');
        Route::get('/inventory/supplies', [InventoryItemController::class, 'suppliesPage'])
            ->name('admin.inventory.supplies');
        Route::get('/inventory/equipment', [InventoryItemController::class, 'equipmentPage'])
            ->name('admin.inventory.equipment');
    });

    Route::middleware('permission:manage inquiries')->group(function () {
        Route::get('/inquiry', [InquiryController::class, 'page'])->name('admin.inquiry');
    });

    Route::middleware('permission:manage room types')->group(function () {
        Route::get('/room_type/index', [RoomTypeController::class, 'page'])->name('admin.room_type.index');
        Route::get('/room-types/create', [RoomTypeController::class, 'createPage'])->name('admin.room_type.create');
        Route::get('/room-types/{id}', [RoomTypeController::class, 'editPage'])->name('admin.room_type.edit');
    });

    Route::middleware('permission:manage rooms')->group(function () {
        Route::get('/room_type/room/add-room', [RoomController::class, 'createPage'])->name('admin.room_type.room.add-room');
        Route::get('/room_type/room/{id}/edit', [RoomController::class, 'editPage'])->name('admin.room_type.room.edit');
    });

    Route::middleware('permission:manage amenities')->group(function () {
        Route::get('/amenities', [AmenityController::class, 'page'])->name('admin.amenities');
    });

    Route::middleware('permission:manage gallery')->group(function () {
        Route::get('/gallery', [GalleryImageController::class, 'page'])->name('admin.gallery');
    });

    Route::middleware('permission:manage website info')->group(function () {
        Route::get('/info', [WebsiteInfoController::class, 'page'])->name('admin.info');
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/home-info', [WebsiteInfoController::class, 'pageGlobal'])->name('admin.home-info');
    });

    Route::middleware('permission:manage reviews')->group(function () {
        Route::get('/reviews', [ReviewController::class, 'page'])->name('admin.reviews');
    });

    Route::middleware('permission:manage users')->group(function () {
        Route::get('/users', [UserController::class, 'page'])->name('admin.users');
    });

    Route::middleware('permission:manage locations')->group(function () {
        Route::get('/locations', [LocationController::class, 'page'])->name('admin.locations');
    });

    Route::middleware('permission:manage logs')->group(function () {
        Route::get('/activity-log', [AdminController::class, 'activityLogPage'])->name('admin.activity-log');
    });

    Route::post('/switch-location', [AdminController::class, 'switchLocation'])->name('admin.switch-location');

    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/password', [AdminController::class, 'updatePassword'])->name('password.update');
});
