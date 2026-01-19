<?php

namespace App\Application\Commerce;

use App\Application\Commerce\Commands\ProcessWebhookCommand;
use App\Application\Commerce\Results\WebhookResult;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Support\Facades\Log;

final class ProcessPaymentWebhookHandler
{
    public function handle(ProcessWebhookCommand $command): WebhookResult
    {
        /** @var Payment|null $payment */
        $payment = Payment::where('transaction_id', $command->transactionId)->first();

        if (! $payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $command->transactionId,
            ]);

            return WebhookResult::paymentNotFound($command->transactionId);
        }

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
    }
}
