<?php

namespace Modules\Commerce\Application;

use Modules\Commerce\Application\Commands\ProcessWebhookCommand;
use Modules\Commerce\Application\Results\WebhookResult;
use Modules\Commerce\Domain\Events\OrderPaid;
use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Payment;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhookHandler
{
    public function handle(ProcessWebhookCommand $command): WebhookResult
    {
        if (! $command->signatureVerified) {
            Log::warning('Webhook signature verification failed', [
                'transaction_id' => $command->transactionId,
            ]);

            return WebhookResult::signatureInvalid();
        }

        $payment = Payment::where('transaction_id', $command->transactionId)->first();

        if (! $payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $command->transactionId,
            ]);

            return WebhookResult::paymentNotFound($command->transactionId);
        }

        if ($payment->isPaid()) {
            Log::info('Payment already processed', [
                'transaction_id' => $command->transactionId,
            ]);

            return WebhookResult::success($payment->order_id, 'Payment already processed');
        }

        Log::info('Processing webhook payload', [
            'transaction_id' => $command->transactionId,
            'status' => $command->status,
            'payload' => $command->payload,
        ]);

        return DB::transaction(function () use ($command, $payment) {
            $payment->update([
                'status' => $command->status,
                'payload' => array_merge($payment->payload ?? [], $command->payload ?? []),
            ]);

            $order = $payment->order;

            if ($command->success) {
                $order->markAsPaid();

                $subscriber = $order->subscriber;
                OrderPaid::dispatch($order, $subscriber);

                Log::info('Order paid successfully', [
                    'order_id' => $order->id,
                    'transaction_id' => $command->transactionId,
                ]);

                return WebhookResult::success($order->id, 'Order paid successfully');
            }

            if ($command->status === Payment::STATUS_FAILED) {
                $order->markAsFailed();

                Log::info('Order payment failed', [
                    'order_id' => $order->id,
                    'transaction_id' => $command->transactionId,
                ]);

                return WebhookResult::success($order->id, 'Order marked as failed');
            }

            return WebhookResult::success($order->id, 'Payment status updated');
        });
    }
}
