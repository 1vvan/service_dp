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
        ]);

        $car->load('carModel.brand');

        return $car;
    }
}