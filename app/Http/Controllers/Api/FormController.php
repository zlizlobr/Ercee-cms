<?php

namespace App\Http\Controllers\Api;

use Modules\Forms\Application\Commands\SubmitFormCommand;
use Modules\Forms\Application\SubmitFormHandler;
use Modules\Forms\Domain\Form;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\SubmitFormRequest;
use Illuminate\Http\JsonResponse;

/**
 * Provide read-only form access and handle form submissions.
 */
class FormController extends ApiController
{
    public function __construct(
        private SubmitFormHandler $submitFormHandler
    ) {}

    /**
     * Return a form schema by ID.
     */
    public function show(int $id): JsonResponse
    {
        return $this->safeGet(function () use ($id) {
            $form = Form::active()->find($id);

            if (! $form) {
                return response()->json(['error' => 'Form not found'], 404);
            }

            $dataOptions = $form->data_options ?? [];

            if (($form->submit_button_text ?? null) !== null && ! array_key_exists('submit_button_text', $dataOptions)) {
                $dataOptions['submit_button_text'] = $form->submit_button_text;
            }

            if (($form->success_title ?? null) !== null && ! array_key_exists('success_title', $dataOptions)) {
                $dataOptions['success_title'] = $form->success_title;
            }

            if (($form->success_message ?? null) !== null && ! array_key_exists('success_message', $dataOptions)) {
                $dataOptions['success_message'] = $form->success_message;
            }

            return response()->json([
                'data' => [
                    'id' => $form->id,
                    'name' => $form->name,
                    'schema' => $form->schema,
                    'data_options' => $dataOptions,
                    'submit_button_text' => $form->submit_button_text,
                    'success_title' => $form->success_title,
                    'success_message' => $form->success_message,
                ],
            ]);
        });
    }

    /**
     * Submit a form with validated input.
     */
    public function submit(SubmitFormRequest $request, int $id): JsonResponse
    {
        $data = $request->except(['email', '_hp_field']);

        $command = new SubmitFormCommand(
            formId: $id,
            email: $request->input('email', ''),
            data: $data,
            source: $request->header('X-Form-Source', 'form:'.$id),
            isHoneypotFilled: $request->filled('_hp_field'),
        );

        $result = $this->submitFormHandler->handle($command);

        if ($result->isHoneypot()) {
            return response()->json(['message' => 'Thank you for your submission.']);
        }

        if (! $result->isSuccess()) {
            $status = $result->error === 'Form not found' ? 404 : 422;

            return response()->json([
                'error' => $result->error,
                'errors' => $result->validationErrors,
            ], $status);
        }

        return response()->json([
            'data' => ['contract_id' => $result->contractId],
        ], 201);
    }
}
