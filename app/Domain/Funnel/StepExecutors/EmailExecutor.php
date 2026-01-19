<?php

namespace App\Domain\Funnel\StepExecutors;

use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\FunnelStep;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Support\Facades\Mail;

class EmailExecutor implements StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array
    {
        $template = $step->config['template'] ?? 'default';
        $subject = $step->config['subject'] ?? 'Message from us';
        $body = $step->config['body'] ?? '';

        Mail::raw($body, function ($message) use ($subscriber, $subject) {
            $message->to($subscriber->email)
                ->subject($subject);
        });

        return [
            'payload' => [
                'template' => $template,
                'subject' => $subject,
                'recipient' => $subscriber->email,
            ],
        ];
    }
}
