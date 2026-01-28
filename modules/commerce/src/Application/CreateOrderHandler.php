<?php

namespace Modules\Commerce\Application;

use Modules\Commerce\Application\Commands\CreateOrderCommand;
use Modules\Commerce\Application\Results\CheckoutResult;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Product;
use App\Domain\Subscriber\SubscriberService;
use Illuminate\Support\Facades\DB;

class CreateOrderHandler
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

        $idempotencyKey = $this->generateIdempotencyKey($product->id, $command->email);

        $existingOrder = Order::where('idempotency_key', $idempotencyKey)
            ->where('status', Order::STATUS_PENDING)
            ->first();

        if ($existingOrder) {
            $redirectUrl = $this->paymentGateway->createPayment($existingOrder);

            return CheckoutResult::success($existingOrder->id, $redirectUrl);
        }

        return DB::transaction(function () use ($command, $product, $idempotencyKey) {
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
                'idempotency_key' => $idempotencyKey,
            ]);

            $redirectUrl = $this->paymentGateway->createPayment($order);

            return CheckoutResult::success($order->id, $redirectUrl);
        });
    }

    private function generateIdempotencyKey(int $productId, string $email): string
    {
        return sha1($productId.$email.now()->toDateString());
    }
}
