<?php

namespace Modules\Funnel\Application;

use Modules\Funnel\Application\Commands\StartFunnelCommand;
use Modules\Funnel\Application\Results\StartFunnelResult;
use Modules\Funnel\Domain\Services\FunnelStarter;
use App\Domain\Subscriber\Subscriber;

class StartFunnelHandler
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
