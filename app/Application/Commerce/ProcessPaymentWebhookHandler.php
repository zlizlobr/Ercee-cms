<?php

namespace App\Application\Commerce;

use App\Application\Commerce\Commands\ProcessWebhookCommand;
use App\Application\Commerce\Results\WebhookResult;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ProcessPaymentWebhookHandler
{
    public function handle(ProcessWebhookCommand $command): WebhookResult
    {
        if (! $command->signatureVerified) {
            Log::warning('Webhook signature verification failed', [
                'transaction_id' => $command->transactionId,
            ]);

            return WebhookResult::signatureInvalid();
        }

        /** @var Payment|null $payment */
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

            /** @var Order $order */
            $order = $payment->order;

            if ($command->success) {
                $order->markAsPaid();

                /** @var Subscriber $subscriber */
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
