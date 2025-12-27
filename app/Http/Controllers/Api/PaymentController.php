<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    public function createCheckoutSession(Booking $booking): JsonResponse
    {
        try {
            $paidTransaction = PaymentTransaction::where('booking_id', $booking->id)
                ->where('payment_status', PaymentTransaction::STATUS_COMPLETED)
                ->first();

            if ($paidTransaction) {
                return response()->json([
                    'message' => 'Цей букінг вже оплачений',
                ], 400);
            }

            $booking->load('client', 'car', 'services');

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'uah',
                        'product_data' => [
                            'name' => 'Запис #' . $booking->id,
                            'description' => 'Оплата за послуги: ' . $booking->services->pluck('name')->join(', '),
                        ],
                        'unit_amount' => (int)($booking->total_price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => config('app.frontend_url') . '/dashboard/bookings?payment=success',
                'cancel_url' => config('app.frontend_url') . '/dashboard/bookings?payment=cancelled',
                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ]);

            PaymentTransaction::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => 'stripe',
                'payment_status' => PaymentTransaction::STATUS_PENDING,
                'transaction_id' => $session->id,
            ]);

            return response()->json([
                'session_id' => $session->id,
                'url' => $session->url,
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Помилка при створенні сесії оплати: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Помилка при створенні сесії оплати',
            ], 500);
        }
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;

            default:
                Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    private function handleCheckoutSessionCompleted($session): void
    {
        $bookingId = $session->metadata->booking_id ?? null;

        if (!$bookingId) {
            Log::warning('Stripe session without booking_id: ' . $session->id);
            return;
        }

        $transaction = PaymentTransaction::where('transaction_id', $session->id)->first();

        if ($transaction) {
            $transaction->update([
                'payment_status' => PaymentTransaction::STATUS_COMPLETED,
            ]);

            $booking = Booking::find($bookingId);
            $booking->update([
                'status_id' => BookingStatus::getPaidStatusId(),
            ]);
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        Log::info('Payment succeeded: ' . $paymentIntent->id);
    }

    private function handlePaymentIntentFailed($paymentIntent): void
    {
        $transaction = PaymentTransaction::where('transaction_id', $paymentIntent->id)->first();

        if ($transaction) {
            $transaction->update([
                'payment_status' => PaymentTransaction::STATUS_FAILED,
            ]);
        }

        Log::info('Payment failed: ' . $paymentIntent->id);
    }
}

