<?php

namespace App\Providers;

use App\Http\Repositories\Booking\BookingRepository;
use App\Http\Repositories\Booking\BookingRepositoryInterface;
use App\Http\Repositories\Car\CarRepository;
use App\Http\Repositories\Car\CarRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
