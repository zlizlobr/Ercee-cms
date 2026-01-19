<?php

namespace App\Application\Form;

use App\Application\Form\Commands\SubmitFormCommand;
use App\Application\Form\Results\SubmitFormResult;
use App\Domain\Form\Contract;
use App\Domain\Form\Events\ContractCreated;
use App\Domain\Form\Form;
use App\Domain\Subscriber\SubscriberService;
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
        ]);

        ContractCreated::dispatch($contract, $subscriber);

        return SubmitFormResult::success($contract->id);
    }
}
