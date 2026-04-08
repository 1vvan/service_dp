<?php

namespace App\Http\Repositories\Car;

use App\Models\ClientCar;

interface CarRepositoryInterface
{
    public function createCar(int $clientId, array $data): ClientCar;
    
    public function updateCar(int $carId, array $data): ClientCar;

    public function confirmCar(int $carId): ClientCar;

    public function createCarFromPublic(int $clientId, array $data): ClientCar;
}