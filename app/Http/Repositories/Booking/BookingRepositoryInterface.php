<?php

namespace App\Http\Repositories\Booking;

use App\Models\Booking;

interface BookingRepositoryInterface
{
    public function createBooking(int $clientId, array $data): Booking;
    
    public function calculateTotalPrice(int $carId, array $serviceIds): float;
    
    public function calculatePriceDetails(int $carId, array $serviceIds): array;
}