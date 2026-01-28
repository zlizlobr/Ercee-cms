<?php

namespace Modules\Commerce\Domain\Gateways;

use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Payment;
use Modules\Commerce\Domain\PaymentResult;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPayment(Order $order): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'czk'),
                    'product_data' => [
                        'name' => $order->product->name,
                    ],
                    'unit_amount' => $order->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => config('services.stripe.success_url').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('services.stripe.cancel_url'),
            'customer_email' => $order->email,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->payments()->create([
            'gateway' => $this->getGatewayName(),
            'transaction_id' => $session->id,
            'status' => Payment::STATUS_PENDING,
            'payload' => ['session_id' => $session->id],
        ]);

        return $session->url;
    }

    public function handleWebhook(Request $request): PaymentResult
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            throw new \InvalidArgumentException('Invalid Stripe webhook signature');
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            return PaymentResult::success(
                transactionId: $session->id,
                payload: [
                    'event_type' => $event->type,
                    'payment_intent' => $session->payment_intent,
                    'customer_email' => $session->customer_email,
                    'amount_total' => $session->amount_total,
                ],
            );
        }

        if ($event->type === 'checkout.session.expired') {
            $session = $event->data->object;

            return PaymentResult::failed(
                transactionId: $session->id,
                payload: ['event_type' => $event->type],
            );
        }

        return PaymentResult::pending(
            transactionId: $event->data->object->id ?? '',
            payload: ['event_type' => $event->type],
        );
    }

    public function getGatewayName(): string
    {
        return Payment::GATEWAY_STRIPE;
    }
}
