<?php

namespace App\Http\Repositories\Car;

use App\Models\ClientCar;
use Illuminate\Support\Facades\Auth;

class CarRepository implements CarRepositoryInterface
{
    public function createCar(int $clientId, array $data): ClientCar
    {
        $data['client_id'] = $clientId;
        $car = ClientCar::create([
            'client_id' => $clientId,
            'car_model_id' => $data['model'],
            'car_year' => $data['year'],
            'mileage' => $data['mileage'],
            'vin' => $data['vin'],
            'license_plate' => $data['licence_plate'],
            'engine_type_id' => $data['engine_type'],
            'gearbox_type_id' => $data['gearbox_type'],
            'drive_unit_type_id' => $data['drive_unit_type'],
            'fuel_type_id' => $data['fuel_type'],
        ]);

        $car->load('carModel.brand');

        return $car;
    }

    public function updateCar(int $carId, array $data): ClientCar
    {
        $car = ClientCar::find($carId);
        $car->update([
            'car_model_id' => $data['model'],
            'car_year' => $data['year'],
            'mileage' => $data['mileage'],
            'vin' => $data['vin'],
            'license_plate' => $data['licence_plate'],
            'engine_type_id' => $data['engine_type'],
            'gearbox_type_id' => $data['gearbox_type'],
            'drive_unit_type_id' => $data['drive_unit_type'],
            'fuel_type_id' => $data['fuel_type'],
        ]);

        $car->load('carModel.brand');

        return $car;
    }

    public function confirmCar(int $carId): ClientCar
    {
        $car = ClientCar::find($carId);
        $user = Auth::user();

        $car->update(['checked_by' => $user->id]);
        return $car;
    }

    public function createCarFromPublic(int $clientId, array $data): ClientCar
    {
        $car = ClientCar::create([
            'client_id' => $clientId,
            'car_model_id' => $data['car_model_id'],
            'car_year' => $data['year'],
            'mileage' => $data['mileage'] ?? 0,
            'vin' => $data['vin'] ?? null,
            'license_plate' => $data['licence_plate'],
            'engine_type_id' => null,
            'gearbox_type_id' => null,
            'drive_unit_type_id' => null,
            'fuel_type_id' => null,
            'checked_by' => null,
        ]);

        $car->load('carModel.brand');
        return $car;
    }
}