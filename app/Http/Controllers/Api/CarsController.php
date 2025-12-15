<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Car\CarRepositoryInterface;
use App\Http\Requests\Car\CreateCarRequest;
use App\Models\Client;
use App\Models\ClientCar;
use App\Models\User;

class CarsController extends Controller
{
    protected $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function index()
    {
        $clientId = request()->input('client_id') ?? null;

        $cars = ClientCar::query()
            ->when($clientId, function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->with('carModel.brand', 'latestBooking', 'fuelType', 'engineType', 'gearboxType', 'driveUnitType')
            ->get();

        return response()->json($cars);
    }

    public function getCar($carId)
    {
        $car = ClientCar::find($carId);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $car->load('carModel.brand', 'latestBooking', 'fuelType', 'engineType', 'gearboxType', 'driveUnitType');

        return response()->json($car);
    }

    public function getUserCars($clientId)
    {
        if (!$clientId) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $cars = ClientCar::query()
            ->where('client_id', $clientId)
            ->with('carModel.brand', 'latestBooking', 'fuelType', 'engineType', 'gearboxType', 'driveUnitType')
            ->get();

        return response()->json($cars);
    }

    public function createCar(Client $client, CreateCarRequest $request)
    {
        return response()->json($this->carRepository->createCar($client->id, $request->all()));
    }

    public function updateCar(ClientCar $car, CreateCarRequest $request)
    {
        return response()->json($this->carRepository->updateCar($car->id, $request->all()));
    }
}