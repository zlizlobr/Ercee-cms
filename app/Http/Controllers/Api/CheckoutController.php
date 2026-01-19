<?php

namespace App\Http\Controllers\Api;

use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\SubscriberService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function __construct(
        private SubscriberService $subscriberService,
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $product = Product::where('active', true)->find($validated['product_id']);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found or inactive',
            ], 404);
        }

        $subscriber = $this->subscriberService->findOrCreateByEmail(
            $validated['email'],
            'checkout:' . $product->id
        );

        $order = Order::create([
            'subscriber_id' => $subscriber->id,
            'product_id' => $product->id,
            'email' => $validated['email'],
            'price' => $product->price,
            'status' => Order::STATUS_PENDING,
        ]);

        $redirectUrl = $this->paymentGateway->createPayment($order);

        return response()->json([
            'message' => 'Checkout initiated',
            'data' => [
                'order_id' => $order->id,
                'redirect_url' => $redirectUrl,
            ],
        ], 201);
    }
}
