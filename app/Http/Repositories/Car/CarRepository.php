<?php

namespace App\Http\Repositories\Car;

use App\Models\ClientCar;

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
}