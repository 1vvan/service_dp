<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Car\CarRepositoryInterface;
use App\Http\Requests\Car\CreateCarRequest;
use App\Models\CarPhoto;
use App\Models\Client;
use App\Models\ClientCar;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $car->load('carModel.brand', 'bookings', 'latestBooking', 'fuelType', 'engineType', 'gearboxType', 'driveUnitType', 'photos');

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

    public function confirmCar(ClientCar $car)
    {
        return response()->json($this->carRepository->confirmCar($car->id));
    }

    public function uploadPhoto(Request $request, ClientCar $car): JsonResponse
    {
        $files = $request->file('photos');
        if (!$files) {
            $files = $request->file('photo') ? [$request->file('photo')] : [];
        }
        if (empty($files)) {
            return response()->json(['message' => 'Виберіть хоча б одне фото'], 422);
        }

        $request->validate([
            'photos' => ['array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ], [], ['photos.*' => 'фото']);

        $uploadedPhotos = [];
        $maxSortOrder = CarPhoto::where('client_car_id', $car->id)->max('sort_order') ?? 0;

        foreach ($files as $file) {
            $path = $file->store('car-photos/' . $car->id, 'public');
            $maxSortOrder++;

            $photo = CarPhoto::create([
                'client_car_id' => $car->id,
                'path' => $path,
                'sort_order' => $maxSortOrder,
            ]);
            $uploadedPhotos[] = $photo->load('clientCar');
        }

        $count = count($uploadedPhotos);
        $message = $count === 1
            ? 'Фото успішно завантажено'
            : "Завантажено $count фото";

        return response()->json([
            'message' => $message,
            'photos' => $uploadedPhotos,
        ], 201);
    }

    public function deletePhoto(ClientCar $car, CarPhoto $photo): JsonResponse
    {
        if ($photo->client_car_id !== $car->id) {
            return response()->json(['message' => 'Фото не належить цьому автомобілю'], 403);
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Фото успішно видалено']);
    }
}