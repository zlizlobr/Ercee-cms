<?php

namespace App\Http\Controllers\Api;

use App\Application\Form\Commands\SubmitFormCommand;
use App\Application\Form\SubmitFormHandler;
use App\Domain\Form\Form;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubmitFormRequest;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function __construct(
        private SubmitFormHandler $submitFormHandler
    ) {}

    public function show(int $id): JsonResponse
    {
        $form = Form::active()->find($id);

        if (! $form) {
            return response()->json(['error' => 'Form not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $form->id,
                'name' => $form->name,
                'schema' => $form->schema,
                'data_options' => $form->data_options ?? [],
                'submit_button_text' => $form->submit_button_text,
                'success_title' => $form->success_title,
                'success_message' => $form->success_message,
            ],
        ]);
    }

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
