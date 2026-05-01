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

    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('room-types', RoomTypeController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('amenities', AmenityController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('gallery-images', GalleryImageController::class);
    Route::apiResource('house-rules', HouseRuleController::class);
    Route::apiResource('guests', GuestController::class);
    Route::apiResource('booking-inquiries', BookingInquiryController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('property-settings', PropertySettingController::class);
    Route::apiResource('check-ins', CheckInController::class);
    Route::apiResource('check-outs', CheckOutController::class);
    Route::apiResource('inquiries', InquiryController::class);
    Route::apiResource('inventory', InventoryController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::patch('reviews/{id}/status', [ReviewController::class, 'updateStatus']);
    Route::apiResource('locations', LocationController::class);
    Route::get('website-info', [WebsiteInfoController::class, 'show']);
    Route::post('website-info', [WebsiteInfoController::class, 'update']);

    Route::apiResource('users', UserController::class);
    Route::post('roles', [UserController::class, 'storeRole']);
    Route::patch('roles/{id}/permissions', [UserController::class, 'updateRolePermissions']);

    Route::middleware('role:super_admin')->group(function () {
        Route::post('roles', [UserController::class, 'storeRole']);
        Route::patch('roles/{id}/permissions', [UserController::class, 'updateRolePermissions']);
    });
});
