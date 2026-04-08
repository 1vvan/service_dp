<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Booking\BookingRepositoryInterface;
use App\Http\Repositories\Car\CarRepositoryInterface;
use App\Http\Requests\Booking\PublicBookingRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    public function __construct(
        protected CarRepositoryInterface $carRepository,
        protected BookingRepositoryInterface $bookingRepository
    ) {
    }

    public function store(PublicBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $booking = DB::transaction(function () use ($data) {
            $client = Client::where('phone', $data['phone'])->first();

            if (!$client) {
                $email = $data['email'] ?? ('public-' . time() . '-' . uniqid() . '@booking.local');
                $client = Client::create([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'email' => $email,
                ]);
            } else {
                $client->update([
                    'full_name' => $data['full_name'],
                    'email' => $data['email'] ?? $client->email,
                ]);
            }

            $licensePlate = trim($data['car']['licence_plate']);

            $car = $client->cars()
                ->whereRaw('LOWER(TRIM(license_plate)) = ?', [mb_strtolower($licensePlate)])
                ->first();

            if (!$car) {
                $car = $this->carRepository->createCarFromPublic($client->id, [
                    'car_model_id' => $data['car']['car_model_id'],
                    'year' => $data['car']['year'],
                    'licence_plate' => $licensePlate,
                    'mileage' => $data['car']['mileage'] ?? 0,
                    'vin' => $data['car']['vin'] ?? null,
                ]);
            }

            return $this->bookingRepository->createBooking($client->id, [
                'car_id' => $car->id,
                'service_ids' => $data['service_ids'],
                'date' => $data['date'],
                'comment' => $data['comment'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Запис успішно створений. Ми зв\'яжемося з вами для підтвердження.',
            'booking' => $booking->load(['car.carModel.brand', 'services', 'status']),
        ], 201);
    }
}
