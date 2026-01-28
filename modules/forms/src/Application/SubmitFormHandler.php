<?php

namespace Modules\Forms\Application;

use App\Domain\Subscriber\SubscriberService;
use Modules\Forms\Application\Commands\SubmitFormCommand;
use Modules\Forms\Application\Results\SubmitFormResult;
use Modules\Forms\Domain\Contract;
use Modules\Forms\Domain\Events\ContractCreated;
use Modules\Forms\Domain\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

final class SubmitFormHandler
{
    public function __construct(
        private SubscriberService $subscriberService
    ) {}

    public function handle(SubmitFormCommand $command): SubmitFormResult
    {
        $form = Form::active()->find($command->formId);

        if (! $form) {
            return SubmitFormResult::formNotFound();
        }

        if ($command->isHoneypotFilled) {
            Log::warning('Honeypot triggered', [
                'form_id' => $command->formId,
            ]);

            return SubmitFormResult::honeypotTriggered();
        }

        $dataWithEmail = array_merge($command->data, ['email' => $command->email]);

        $rules = array_merge($form->getValidationRules(), ['email' => ['required', 'email']]);

        $validator = Validator::make($dataWithEmail, $rules);

        if ($validator->fails()) {
            return SubmitFormResult::validationFailed($validator->errors()->toArray());
        }

        $idempotencyKey = $this->generateIdempotencyKey($form->id, $command->email);

        $existingContract = Contract::where('idempotency_key', $idempotencyKey)->first();

        if ($existingContract) {
            return SubmitFormResult::success($existingContract->id);
        }

        return DB::transaction(function () use ($command, $form, $idempotencyKey) {
            $subscriber = $this->subscriberService->findOrCreateByEmail(
                $command->email,
                $command->source
            );

            $contract = Contract::create([
                'subscriber_id' => $subscriber->id,
                'form_id' => $form->id,
                'email' => $command->email,
                'data' => $command->data,
                'source' => $command->source,
                'status' => Contract::STATUS_NEW,
                'idempotency_key' => $idempotencyKey,
            ]);

            ContractCreated::dispatch($contract, $subscriber);

            return SubmitFormResult::success($contract->id);
        });
    }

    private function generateIdempotencyKey(int $formId, string $email): string
    {
        return sha1($formId.$email.now()->toDateString());
    }
}
