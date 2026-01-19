<?php

namespace App\Http\Controllers\Api;

use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function stripe(Request $request): Response
    {
        try {
            $result = $this->paymentGateway->handleWebhook($request);
        } catch (\InvalidArgumentException $e) {
            Log::warning('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response('Webhook processing failed', 500);
        }

        $payment = Payment::where('transaction_id', $result->transactionId)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $result->transactionId,
            ]);

            return response('Payment not found', 404);
        }

        $payment->update([
            'status' => $result->status,
            'payload' => array_merge($payment->payload ?? [], $result->payload ?? []),
        ]);

        $order = $payment->order;

        if ($result->success) {
            $order->markAsPaid();

            OrderPaid::dispatch($order, $order->subscriber);

            Log::info('Order paid successfully', [
                'order_id' => $order->id,
                'transaction_id' => $result->transactionId,
            ]);
        } elseif ($result->status === Payment::STATUS_FAILED) {
            $order->markAsFailed();

            Log::info('Order payment failed', [
                'order_id' => $order->id,
                'transaction_id' => $result->transactionId,
            ]);
        }

        return response('OK', 200);
    }
}
