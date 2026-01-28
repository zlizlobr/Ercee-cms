<?php

namespace Modules\Funnel\Domain\StepExecutors;

use Modules\Funnel\Domain\FunnelRun;
use Modules\Funnel\Domain\FunnelStep;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Support\Facades\Http;

class WebhookExecutor implements StepExecutorInterface
{
    public function execute(FunnelStep $step, FunnelRun $run, Subscriber $subscriber): array
    {
        $url = $step->config['url'] ?? '';
        $method = strtoupper($step->config['method'] ?? 'POST');
        $headers = $step->config['headers'] ?? [];
        $body = $step->config['body'] ?? [];

        $body = $this->replacePlaceholders($body, $subscriber, $run);

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->{strtolower($method)}($url, $body);

        if ($response->failed()) {
            throw new \RuntimeException("Webhook failed with status {$response->status()}: {$response->body()}");
        }

        return [
            'payload' => [
                'url' => $url,
                'method' => $method,
                'status_code' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ],
        ];
    }

    protected function replacePlaceholders(array $body, Subscriber $subscriber, FunnelRun $run): array
    {
        $replacements = [
            '{{subscriber_id}}' => $subscriber->id,
            '{{subscriber_email}}' => $subscriber->email,
            '{{funnel_run_id}}' => $run->id,
            '{{funnel_id}}' => $run->funnel_id,
        ];

        array_walk_recursive($body, function (&$value) use ($replacements) {
            if (is_string($value)) {
                $value = str_replace(array_keys($replacements), array_values($replacements), $value);
            }
        });

        return $body;
    }
}
