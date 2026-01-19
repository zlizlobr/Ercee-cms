<?php

namespace App\Http\Controllers\Api;

use App\Domain\Form\Contract;
use App\Domain\Form\Events\ContractCreated;
use App\Domain\Form\Form;
use App\Domain\Subscriber\SubscriberService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function __construct(
        private SubscriberService $subscriberService
    ) {}

    public function submit(Request $request, int $id): JsonResponse
    {
        $form = Form::active()->find($id);

        if (!$form) {
            return response()->json([
                'error' => 'Form not found',
            ], 404);
        }

        // Honeypot check
        if ($request->filled('_hp_field')) {
            Log::warning('Honeypot triggered', [
                'form_id' => $id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Thank you for your submission.',
            ]);
        }

        // Validate using form schema
        $rules = $form->getValidationRules();
        $rules['email'] = ['required', 'email'];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $email = $validated['email'];
        unset($validated['email']);

        // Find or create subscriber
        $source = $request->header('X-Form-Source', 'form:' . $form->id);
        $subscriber = $this->subscriberService->findOrCreateByEmail($email, $source);

        // Create contract
        $contract = Contract::create([
            'subscriber_id' => $subscriber->id,
            'form_id' => $form->id,
            'email' => $email,
            'data' => $validated,
            'source' => $source,
            'status' => Contract::STATUS_NEW,
        ]);

        // Dispatch event
        ContractCreated::dispatch($contract, $subscriber);

        return response()->json([
            'message' => 'Form submitted successfully.',
            'data' => [
                'contract_id' => $contract->id,
            ],
        ], 201);
    }
}
