<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\HouseRuleController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingInquiryController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertySettingController;
use App\Http\Controllers\WebsiteInfoController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LocationController;

// ── Public API routes (no auth required) ─────────────────────────
Route::get('room-types/{id}/availability', [RoomTypeController::class, 'availability']);
Route::get('website-info', [WebsiteInfoController::class, 'show']);
Route::post('reviews/verified', [ReviewController::class, 'storeVerified']);

// ── Authenticated API routes ──────────────────────────────────────
Route::middleware(['web', 'auth'])->group(function () {

    // ── Bookings ──────────────────────────────────────────────────────
    Route::middleware('permission:view bookings')->group(function () {
        Route::apiResource('bookings', BookingController::class)->only(['index', 'show']);
        Route::apiResource('guests', GuestController::class)->only(['index', 'show']);
        Route::apiResource('check-ins', CheckInController::class)->only(['index', 'show']);
        Route::apiResource('check-outs', CheckOutController::class)->only(['index', 'show']);
        Route::apiResource('payments', PaymentController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:create bookings')->group(function () {
        Route::apiResource('bookings', BookingController::class)->only(['store']);
        Route::apiResource('guests', GuestController::class)->only(['store']);
    });

    Route::middleware('permission:edit bookings')->group(function () {
        Route::apiResource('bookings', BookingController::class)->only(['update']);
        Route::apiResource('guests', GuestController::class)->only(['update', 'destroy']);
        Route::post('guests/{id}/restore', [GuestController::class, 'restore']);
        Route::delete('guests/{id}/force', [GuestController::class, 'forceDelete']);
        Route::apiResource('payments', PaymentController::class)->only(['store', 'update', 'destroy']);
    });

    Route::middleware('permission:view bookings')->group(function () {
        Route::get('guests/trashed', [GuestController::class, 'trashed']);
    });

    Route::middleware('permission:cancel bookings')->group(function () {
        Route::apiResource('bookings', BookingController::class)->only(['destroy']);
        Route::post('bookings/{id}/restore', [BookingController::class, 'restore']);
        Route::delete('bookings/{id}/force', [BookingController::class, 'forceDelete']);
    });

    Route::middleware('permission:view bookings')->group(function () {
        Route::get('bookings/trashed', [BookingController::class, 'trashed']);
    });

    // ── Check-in / Check-out ──────────────────────────────────────────
    Route::middleware('permission:check-in guests')->group(function () {
        Route::apiResource('check-ins', CheckInController::class)->only(['store', 'update', 'destroy']);
    });

    Route::middleware('permission:check-out guests')->group(function () {
        Route::apiResource('check-outs', CheckOutController::class)->only(['store', 'update', 'destroy']);
    });

    // ── Inventory ─────────────────────────────────────────────────────
    Route::middleware('permission:view inventory')->group(function () {
        Route::apiResource('inventory', InventoryController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:edit inventory')->group(function () {
        Route::apiResource('inventory', InventoryController::class)->only(['store', 'update', 'destroy']);
    });

    // ── Room Types ────────────────────────────────────────────────────
    Route::middleware('permission:manage room types')->group(function () {
        Route::apiResource('room-types', RoomTypeController::class);
        Route::get('room-types/trashed', [RoomTypeController::class, 'trashed']);
        Route::post('room-types/{id}/restore', [RoomTypeController::class, 'restore']);
        Route::delete('room-types/{id}/force', [RoomTypeController::class, 'forceDelete']);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('house-rules', HouseRuleController::class);
    });

    // ── Rooms ─────────────────────────────────────────────────────────
    Route::middleware('permission:manage rooms')->group(function () {
        Route::apiResource('rooms', RoomController::class);
        Route::get('rooms/trashed', [RoomController::class, 'trashed']);
        Route::post('rooms/{id}/restore', [RoomController::class, 'restore']);
        Route::delete('rooms/{id}/force', [RoomController::class, 'forceDelete']);
    });

    // ── Amenities ─────────────────────────────────────────────────────
    Route::middleware('permission:manage amenities')->group(function () {
        Route::apiResource('amenities', AmenityController::class);
    });

    // ── Gallery ───────────────────────────────────────────────────────
    Route::middleware('permission:manage gallery')->group(function () {
        Route::apiResource('gallery-images', GalleryImageController::class);
    });

    // ── Inquiries ─────────────────────────────────────────────────────
    Route::middleware('permission:manage inquiries')->group(function () {
        Route::apiResource('inquiries', InquiryController::class);
        Route::post('inquiries/{id}/reply', [InquiryController::class, 'reply']);
        Route::apiResource('booking-inquiries', BookingInquiryController::class);
    });

    // ── Reviews ───────────────────────────────────────────────────────
    Route::middleware('permission:manage reviews')->group(function () {
        Route::apiResource('reviews', ReviewController::class);
        Route::patch('reviews/{id}/status', [ReviewController::class, 'updateStatus']);
    });

    // ── Website Info & Property Settings ─────────────────────────────
    Route::middleware('permission:manage website info')->group(function () {
        Route::get('website-info', [WebsiteInfoController::class, 'show']);
        Route::post('website-info', [WebsiteInfoController::class, 'update']);
        Route::post('home-info', [WebsiteInfoController::class, 'updateGlobal']);
        Route::apiResource('property-settings', PropertySettingController::class);
    });

    // ── Locations ─────────────────────────────────────────────────────
    Route::middleware('permission:manage locations')->group(function () {
        Route::apiResource('locations', LocationController::class);
    });

    // ── Users & Roles ─────────────────────────────────────────────────
    Route::middleware('permission:manage users')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::post('roles', [UserController::class, 'storeRole']);
        Route::patch('roles/{id}/permissions', [UserController::class, 'updateRolePermissions']);
    });
});
