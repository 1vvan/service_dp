<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Booking\BookingRepositoryInterface;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index()
    {
        return Booking::all();
    }

    public function getUserBookings(Client $client)
    {
        if (!$client) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $bookings = $client->bookings()->with('car', 'services', 'status')->get();

        return response()->json($bookings);
    }

    public function createBooking(Client $client, CreateBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->createBooking($client->id, $request->validated());

            return response()->json([
                'message' => 'Запис успішно створений',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка при створенні запису: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function calculatePrice(): JsonResponse
    {
        $carId = request()->input('car_id');
        $serviceIds = request()->input('service_ids', []);

        if (!$carId || empty($serviceIds)) {
            return response()->json([
                'services' => [],
                'total_price' => 0,
            ]);
        }

        $priceDetails = $this->bookingRepository->calculatePriceDetails($carId, $serviceIds);

        return response()->json($priceDetails);
    }
}