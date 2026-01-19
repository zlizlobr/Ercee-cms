<?php

namespace App\Application\Funnel;

use App\Application\Funnel\Commands\StartFunnelCommand;
use App\Application\Funnel\Results\StartFunnelResult;
use App\Domain\Funnel\Services\FunnelStarter;
use App\Domain\Subscriber\Subscriber;

final class StartFunnelHandler
{
    public function __construct(
        private FunnelStarter $funnelStarter
    ) {}

    public function handle(StartFunnelCommand $command): StartFunnelResult
    {
        $subscriber = Subscriber::find($command->subscriberId);

        if (! $subscriber) {
            return StartFunnelResult::subscriberNotFound();
        }

        $runs = $this->funnelStarter->startByTrigger(
            $command->trigger,
            $subscriber
        );

        if (empty($runs)) {
            return StartFunnelResult::noFunnelsTriggered();
        }

        $runIds = array_map(fn ($run) => $run->id, $runs);

        return StartFunnelResult::success($runIds);
    }
}
