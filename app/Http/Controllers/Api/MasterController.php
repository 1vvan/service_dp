<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Booking\BookingRepositoryInterface;
use App\Mail\BookingCompletedMail;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MasterController extends Controller
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $masterId = $request->user()->id;

        $bookings = Booking::where('master_id', $masterId)
            ->when($request->input('date'), fn ($q, $date) =>
                $q->whereDate('date', $date)
            )
            ->when($request->input('status_id'), fn ($q, $statusId) =>
                $q->where('status_id', $statusId)
            )
            ->with('car.carModel.brand', 'services', 'status', 'client')
            ->whereNotIn('status_id', [BookingStatus::getNewStatusId()])
            ->orderBy('date')
            ->get();

        return response()->json($bookings);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->master_id !== $request->user()->id) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        return response()->json(
            $booking->load('car.carModel.brand', 'services', 'status', 'client')
        );
    }

    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->master_id !== $request->user()->id) {
            return response()->json(['message' => 'Доступ заборонено'], 403);
        }

        $request->validate([
            'status_id' => ['required', 'integer', 'exists:booking_statuses,id'],
        ]);

        $allowedTransitions = [
            BookingStatus::getConfirmedStatusId()  => BookingStatus::getInProgressStatusId(),
            BookingStatus::getInProgressStatusId() => BookingStatus::getPendingPaymentStatusId(),
        ];

        $newStatusId = (int) $request->input('status_id');
        $currentStatusId = $booking->status_id;

        if (!isset($allowedTransitions[$currentStatusId]) || $allowedTransitions[$currentStatusId] !== $newStatusId) {
            return response()->json(['message' => 'Недозволений перехід статусу'], 422);
        }

        $updated = $this->bookingRepository->updateStatus($booking->id, $newStatusId);

        // Notify the client when work is done and payment is expected
        if ($newStatusId === BookingStatus::getPendingPaymentStatusId()) {
            $updated->load(['client', 'car.carModel.brand', 'services', 'status']);
            $email = $updated->client?->email;
            if ($email && !str_contains($email, '@booking.local')) {
                try {
                    Mail::to($email)->send(new BookingCompletedMail($updated));
                } catch (\Throwable $e) {
                    \Log::warning('Failed to send booking receipt email: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => 'Статус оновлено',
            'booking' => $updated,
        ]);
    }

    public function assignMaster(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'master_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $masterId = $request->input('master_id');

        if ($masterId !== null) {
            $master = User::find($masterId);
            if (!$master || $master->role_id !== User::ROLE_MASTER) {
                return response()->json(['message' => 'Вказаний користувач не є майстром'], 422);
            }
        }

        $updated = $this->bookingRepository->assignMaster($booking->id, $masterId);

        return response()->json([
            'message' => $masterId ? 'Майстра призначено' : 'Майстра знято',
            'booking' => $updated,
        ]);
    }

    public function masters(): JsonResponse
    {
        $masters = User::where('role_id', User::ROLE_MASTER)
            ->select('id', 'full_name', 'email', 'phone')
            ->orderBy('full_name')
            ->get();

        return response()->json($masters);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'master_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
            'exclude_booking_id' => ['nullable', 'integer'],
        ]);

        $masterId = (int) $request->input('master_id');
        $date = \Carbon\Carbon::parse($request->input('date'));
        $excludeId = $request->input('exclude_booking_id');

        $conflict = Booking::where('master_id', $masterId)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $date->toDateString())
            ->whereBetween('date', [
                $date->copy()->subHour(),
                $date->copy()->addHour(),
            ])
            ->whereNotIn('status_id', [
                BookingStatus::getCancelledStatusId(),
                BookingStatus::getCompletedStatusId(),
            ])
            ->exists();

        return response()->json([
            'available' => !$conflict,
            'message' => $conflict ? 'Майстер зайнятий у цей час' : 'Майстер вільний',
        ]);
    }
}
