<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\HouseRuleController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingInquiryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertySettingController;

Route::apiResource('bookings', BookingController::class);
Route::apiResource('users', UserController::class);
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
