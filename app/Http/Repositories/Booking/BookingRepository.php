<?php

namespace App\Http\Repositories\Booking;

use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Service;
use App\Models\ClientCar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingRepository implements BookingRepositoryInterface
{
    public function createBooking(int $clientId, array $data): Booking
    {
        $newStatusId = BookingStatus::getNewStatusId();

        $priceDetails = $this->calculatePriceDetails($data['car_id'], $data['service_ids']);
        $totalPrice = $priceDetails['total_price'];

        $booking = Booking::create([
            'client_id' => $clientId,
            'car_id' => $data['car_id'],
            'date' => $data['date'],
            'description' => $data['comment'] ?? null,
            'status_id' => $newStatusId,
            'manager_id' => null,
            'total_price' => $totalPrice,
            'price_calculation' => $priceDetails,
        ]);

        if (!empty($data['service_ids'])) {
            $booking->services()->attach($data['service_ids']);
        }

        $booking->load(['car.carModel.brand', 'services', 'client', 'status']);

        return $booking;
    }

    public function calculateTotalPrice(int $carId, array $serviceIds): float
    {
        if (empty($serviceIds)) {
            return 0;
        }

        $services = Service::whereIn('id', $serviceIds)->get();
        $basePrice = $services->sum('base_price');

        $car = ClientCar::find($carId);
        if (!$car || !$car->car_year) {
            return $basePrice;
        }

        $currentYear = (int) date('Y');
        $carYear = (int) $car->car_year;
        $yearsOld = max(0, $currentYear - $carYear);
        
        $yearModifierPercent = min(50, $yearsOld * 5);
        $yearModifier = ($basePrice * $yearModifierPercent) / 100;

        return round($basePrice + $yearModifier, 2);
    }

    public function calculatePriceDetails(int $carId, array $serviceIds): array
    {
        if (empty($serviceIds)) {
            return [
                'services' => [],
                'base_price' => 0,
                'car_year' => null,
                'years_old' => 0,
                'year_modifier_percent' => 0,
                'year_modifier_amount' => 0,
                'total_price' => 0,
            ];
        }

        $services = Service::whereIn('id', $serviceIds)->get();
        $basePrice = $services->sum('base_price');

        $car = ClientCar::find($carId);
        $yearModifierPercent = 0;
        $carYear = null;
        $yearsOld = 0;
        
        if ($car && $car->car_year) {
            $currentYear = (int) date('Y');
            $carYear = (int) $car->car_year;
            $yearsOld = max(0, $currentYear - $carYear);
            $yearModifierPercent = min(10, $yearsOld * 0.5);
        }

        $yearModifierAmount = ($basePrice * $yearModifierPercent) / 100;

        $servicesDetails = [];
        foreach ($services as $service) {
            $serviceBasePrice = (float) $service->base_price;
            $serviceModifier = ($serviceBasePrice * $yearModifierPercent) / 100;
            $serviceFinalPrice = round($serviceBasePrice + $serviceModifier, 2);
            
            $servicesDetails[] = [
                'id' => $service->id,
                'name' => $service->name,
                'base_price' => $serviceBasePrice,
                'modifier_percent' => $yearModifierPercent,
                'modifier_amount' => round($serviceModifier, 2),
                'final_price' => $serviceFinalPrice,
            ];
        }

        $totalPrice = round($basePrice + $yearModifierAmount, 2);

        return [
            'services' => $servicesDetails,
            'base_price' => round($basePrice, 2),
            'car_year' => $carYear,
            'years_old' => $yearsOld,
            'year_modifier_percent' => $yearModifierPercent,
            'year_modifier_amount' => round($yearModifierAmount, 2),
            'total_price' => $totalPrice,
        ];
    }
}