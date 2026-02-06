<?php

namespace App\Http\Controllers\Api;

use Modules\Commerce\Application\Commands\CreateOrderCommand;
use Modules\Commerce\Application\CreateOrderHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use Illuminate\Http\JsonResponse;

/**
 * Handle checkout requests and order creation.
 */
class CheckoutController extends Controller
{
    public function __construct(
        private CreateOrderHandler $createOrderHandler
    ) {}

    /**
     * Create an order for a product checkout.
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new CreateOrderCommand(
            productId: $validated['product_id'],
            email: $validated['email'],
        );

        $result = $this->createOrderHandler->handle($command);

        if (! $result->isSuccess()) {
            return response()->json(['error' => $result->error], 404);
        }

        return response()->json([
            'data' => [
                'order_id' => $result->orderId,
                'redirect_url' => $result->redirectUrl,
            ],
        ], 201);
    }
}
