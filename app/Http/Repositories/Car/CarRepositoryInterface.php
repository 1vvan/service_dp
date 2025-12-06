<?php

namespace App\Http\Repositories\Car;

use App\Models\ClientCar;

interface CarRepositoryInterface
{
    public function createCar(int $clientId, array $data): ClientCar;
}