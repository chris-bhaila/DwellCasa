<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\BookingRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\ServiceRepositoryInterface;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\HouseRuleRepositoryInterface;
use App\Contracts\GuestRepositoryInterface;
use App\Contracts\BookingInquiryRepositoryInterface;
use App\Contracts\PaymentRepositoryInterface;
use App\Contracts\PropertySettingRepositoryInterface;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoomTypeRepository;
use App\Repositories\RoomRepository;
use App\Repositories\AmenityRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\GalleryImageRepository;
use App\Repositories\HouseRuleRepository;
use App\Repositories\GuestRepository;
use App\Repositories\BookingInquiryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PropertySettingRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoomTypeRepositoryInterface::class, RoomTypeRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(AmenityRepositoryInterface::class, AmenityRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(GalleryImageRepositoryInterface::class, GalleryImageRepository::class);
        $this->app->bind(HouseRuleRepositoryInterface::class, HouseRuleRepository::class);
        $this->app->bind(GuestRepositoryInterface::class, GuestRepository::class);
        $this->app->bind(BookingInquiryRepositoryInterface::class, BookingInquiryRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PropertySettingRepositoryInterface::class, PropertySettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
