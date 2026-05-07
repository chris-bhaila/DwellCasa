<?php

namespace App\Providers;

use App\Contracts\AmenityRepositoryInterface;
use App\Contracts\InquiryRepositoryInterface;
use App\Contracts\BookingRepositoryInterface;
use App\Contracts\CheckInRepositoryInterface;
use App\Contracts\CheckOutRepositoryInterface;
use App\Contracts\GalleryImageRepositoryInterface;
use App\Contracts\GuestRepositoryInterface;
use App\Contracts\HouseRuleRepositoryInterface;
use App\Contracts\PaymentRepositoryInterface;
use App\Contracts\PropertySettingRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomTypeRepositoryInterface;
use App\Contracts\ServiceRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\AmenityRepository;
use App\Repositories\InquiryRepository;
use App\Repositories\BookingRepository;
use App\Repositories\CheckInRepository;
use App\Repositories\CheckOutRepository;
use App\Repositories\GalleryImageRepository;
use App\Repositories\GuestRepository;
use App\Repositories\HouseRuleRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PropertySettingRepository;
use App\Repositories\RoomRepository;
use App\Repositories\RoomTypeRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\WebsiteInfoRepositoryInterface;
use App\Repositories\WebsiteInfoRepository;
use App\Contracts\InventoryRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Contracts\ReviewRepositoryInterface;
use App\Repositories\ReviewRepository;
use App\Contracts\LocationRepositoryInterface;
use App\Repositories\LocationRepository;
use App\Contracts\InventoryCategoryRepositoryInterface;
use App\Repositories\InventoryCategoryRepository;
use App\Contracts\InventoryItemRepositoryInterface;
use App\Repositories\InventoryItemRepository;
use App\Contracts\InventoryStockRepositoryInterface;
use App\Repositories\InventoryStockRepository;
use App\Contracts\InventoryEquipmentRepositoryInterface;
use App\Repositories\InventoryEquipmentRepository;

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
        $this->app->bind(InquiryRepositoryInterface::class, InquiryRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PropertySettingRepositoryInterface::class, PropertySettingRepository::class);
        $this->app->bind(CheckInRepositoryInterface::class, CheckInRepository::class);
        $this->app->bind(CheckOutRepositoryInterface::class, CheckOutRepository::class);
        $this->app->bind(WebsiteInfoRepositoryInterface::class, WebsiteInfoRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(InventoryCategoryRepositoryInterface::class, InventoryCategoryRepository::class);
        $this->app->bind(InventoryItemRepositoryInterface::class, InventoryItemRepository::class);
        $this->app->bind(InventoryStockRepositoryInterface::class, InventoryStockRepository::class);
        $this->app->bind(InventoryEquipmentRepositoryInterface::class, InventoryEquipmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (\App\Models\User $user, string $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        view()->composer('layouts.app', function ($view) {
            $view->with('webInfo', \App\Models\WebsiteInfo::first() ?? new \App\Models\WebsiteInfo());
        });
        view()->composer('layouts.app', function ($view) {
            // Resolve location from current route
            $location = request()->route('location');
    
            // Resolve webInfo based on location
            $webInfo = $location
                ? \App\Models\WebsiteInfo::where('location_id', $location->id)->first()
                : \App\Models\WebsiteInfo::first() ?? new \App\Models\WebsiteInfo();
    
            $view->with('location', $location);
            $view->with('webInfo', $webInfo);
        });
    }
}
