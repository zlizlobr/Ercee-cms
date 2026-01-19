<?php

namespace App\Application\Commerce;

use App\Application\Commerce\Commands\CreateOrderCommand;
use App\Application\Commerce\Results\CheckoutResult;
use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\SubscriberService;

final class CreateOrderHandler
{
    public function __construct(
        private SubscriberService $subscriberService,
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function handle(CreateOrderCommand $command): CheckoutResult
    {
        $product = Product::where('active', true)->find($command->productId);

        if (! $product) {
            return CheckoutResult::productNotFound();
        }

        $subscriber = $this->subscriberService->findOrCreateByEmail(
            $command->email,
            'checkout:'.$product->id
        );

        $order = Order::create([
            'subscriber_id' => $subscriber->id,
            'product_id' => $product->id,
            'email' => $command->email,
            'price' => $product->price,
            'status' => Order::STATUS_PENDING,
        ]);

        $redirectUrl = $this->paymentGateway->createPayment($order);

        return CheckoutResult::success($order->id, $redirectUrl);
    }
}
