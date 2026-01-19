<?php

namespace App\Http\Controllers\Api;

use App\Application\Commerce\Commands\CreateOrderCommand;
use App\Application\Commerce\CreateOrderHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function __construct(
        private CreateOrderHandler $createOrderHandler
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

        $command = new CreateOrderCommand(
            productId: $validated['product_id'],
            email: $validated['email'],
        );

        $result = $this->createOrderHandler->handle($command);

        if (! $result->isSuccess()) {
            return response()->json(['error' => $result->error], 404);
        }

        return response()->json([
            'message' => 'Checkout initiated',
            'data' => [
                'order_id' => $result->orderId,
                'redirect_url' => $result->redirectUrl,
            ],
        ], 201);
    }
}
