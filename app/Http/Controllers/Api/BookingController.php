<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Booking\BookingRepositoryInterface;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function index()
    {
        return Booking::with('car', 'services', 'status', 'paymentTransactions')->get();
    }

    public function getUpcomingBookings()
    {
        $date = request()->input('date') ?? now();
        $clientId = request()->input('client_id') ?? null;

        $bookings = Booking::query()
            ->when($clientId, function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->where('date', '>=', $date)
            ->with('car', 'services', 'status', 'paymentTransactions')
            ->limit(10)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($bookings->load('car', 'services', 'status', 'paymentTransactions'));
    }

    public function getUserBookings(Client $client)
    {
        if (!$client) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $bookings = $client->bookings()->with('car', 'services', 'status', 'paymentTransactions')->get();

        return response()->json($bookings);
    }

    public function getBooking(Booking $booking)
    {
        return response()->json($booking->load('car', 'services', 'status', 'paymentTransactions'));
    }

    public function updateBooking(Booking $booking, UpdateBookingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if (array_key_exists('master_id', $data)) {
                $masterId = $data['master_id'];

                if ($masterId !== null) {
                    $master = \App\Models\User::find($masterId);
                    if (!$master || $master->role_id !== \App\Models\User::ROLE_MASTER) {
                        return response()->json(['message' => 'Вказаний користувач не є майстром'], 422);
                    }

                    $bookingDate = \Carbon\Carbon::parse($data['date']);
                    $conflict = Booking::where('master_id', $masterId)
                        ->where('id', '!=', $booking->id)
                        ->whereDate('date', $bookingDate->toDateString())
                        ->whereBetween('date', [
                            $bookingDate->copy()->subHour(),
                            $bookingDate->copy()->addHour(),
                        ])
                        ->whereNotIn('status_id', [
                            \App\Models\BookingStatus::getCancelledStatusId(),
                            \App\Models\BookingStatus::getCompletedStatusId(),
                        ])
                        ->exists();

                    if ($conflict) {
                        return response()->json([
                            'message' => 'Майстер зайнятий у цей час. Оберіть іншого майстра або змініть час.',
                        ], 422);
                    }
                }
            }

            $booking = $this->bookingRepository->updateBooking($booking->id, $data);

            return response()->json([
                'message' => 'Запис успішно оновлений',
                'booking' => $booking,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка при оновленні запису: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createBooking(Client $client, CreateBookingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $masterId = $data['master_id'] ?? null;

            if ($masterId) {
                $master = \App\Models\User::find($masterId);
                if (!$master || $master->role_id !== \App\Models\User::ROLE_MASTER) {
                    return response()->json(['message' => 'Вказаний користувач не є майстром'], 422);
                }

                $bookingDate = \Carbon\Carbon::parse($data['date']);
                $conflict = Booking::where('master_id', $masterId)
                    ->whereDate('date', $bookingDate->toDateString())
                    ->whereBetween('date', [
                        $bookingDate->copy()->subHour(),
                        $bookingDate->copy()->addHour(),
                    ])
                    ->whereNotIn('status_id', [
                        \App\Models\BookingStatus::getCancelledStatusId(),
                        \App\Models\BookingStatus::getCompletedStatusId(),
                    ])
                    ->exists();

                if ($conflict) {
                    return response()->json([
                        'message' => 'Майстер зайнятий у цей час. Оберіть іншого майстра або змініть час.',
                    ], 422);
                }
            }

            $booking = $this->bookingRepository->createBooking($client->id, $data);

            if ($masterId) {
                $this->bookingRepository->assignMaster($booking->id, $masterId);
                $booking->refresh();
                $booking->load(['master']);
            }

            $this->sendConfirmedMail($booking);

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

    public function generateReceipt(Booking $booking)
    {
        $booking->load(['client', 'car.carModel.brand', 'services', 'status']);
        
        $pdf = Pdf::loadView('receipts.booking', [
            'booking' => $booking,
            'priceCalculation' => $booking->price_calculation ?? [],
        ]);

        return $pdf->download('receipt-booking-' . $booking->id . '.pdf');
    }

    public function markPaid(Booking $booking): JsonResponse
    {
        if ($booking->status_id !== \App\Models\BookingStatus::getPendingPaymentStatusId()) {
            return response()->json(['message' => 'Запис не очікує оплати'], 422);
        }

        $booking->update(['status_id' => \App\Models\BookingStatus::getCompletedStatusId()]);

        return response()->json([
            'message' => 'Запис позначено як сплачений',
            'booking' => $booking->load(['car', 'services', 'status', 'client', 'master', 'paymentTransactions']),
        ]);
    }

    private function sendConfirmedMail(Booking $booking): void
    {
        $booking->loadMissing(['client', 'car.carModel.brand', 'services', 'status', 'master']);
        $email = $booking->client?->email;

        if (!$email || str_contains($email, '@booking.local')) {
            return;
        }

        try {
            Mail::to($email)->send(new BookingConfirmedMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Failed to send booking confirmed email: ' . $e->getMessage());
        }
    }

    public function confirmBooking(Booking $booking, \Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'master_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['nullable', 'date'],
        ]);

        $masterId = (int) $request->input('master_id');

        $master = \App\Models\User::find($masterId);
        if (!$master || $master->role_id !== \App\Models\User::ROLE_MASTER) {
            return response()->json(['message' => 'Вказаний користувач не є майстром'], 422);
        }

        $bookingDate = $request->input('date')
            ? \Carbon\Carbon::parse($request->input('date'))
            : $booking->date;

        $conflict = Booking::where('master_id', $masterId)
            ->where('id', '!=', $booking->id)
            ->whereDate('date', $bookingDate->toDateString())
            ->whereBetween('date', [
                $bookingDate->copy()->subHour(),
                $bookingDate->copy()->addHour(),
            ])
            ->whereNotIn('status_id', [
                \App\Models\BookingStatus::getCancelledStatusId(),
                \App\Models\BookingStatus::getCompletedStatusId(),
            ])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Майстер зайнятий у цей час. Оберіть іншого майстра або змініть час.',
            ], 422);
        }

        if ($request->input('date')) {
            $booking->update(['date' => $bookingDate]);
        }

        $this->bookingRepository->assignMaster($booking->id, $masterId);
        $confirmed = $this->bookingRepository->confirmBooking($booking->id);
        $confirmed->load(['master', 'car.carModel.brand', 'services', 'status', 'client']);

        $this->sendConfirmedMail($confirmed);

        return response()->json([
            'message' => 'Запис підтверджено та майстра призначено',
            'booking' => $confirmed,
        ]);
    }
}