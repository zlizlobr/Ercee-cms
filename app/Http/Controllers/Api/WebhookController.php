<?php

namespace App\Http\Controllers\Api;

use Modules\Commerce\Application\Commands\ProcessWebhookCommand;
use Modules\Commerce\Application\ProcessPaymentWebhookHandler;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway,
        private ProcessPaymentWebhookHandler $webhookHandler
    ) {}

    public function stripe(Request $request): Response
    {
        try {
            $gatewayResult = $this->paymentGateway->handleWebhook($request);
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

        $command = new ProcessWebhookCommand(
            transactionId: $gatewayResult->transactionId,
            status: $gatewayResult->status,
            success: $gatewayResult->success,
            payload: $gatewayResult->payload,
        );

        $result = $this->webhookHandler->handle($command);

        if (! $result->isSuccess()) {
            return response($result->error, 404);
        }

        return response('OK', 200);
    }
}
