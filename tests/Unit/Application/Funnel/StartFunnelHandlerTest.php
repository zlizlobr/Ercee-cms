<?php

namespace Tests\Unit\Application\Funnel;

use App\Application\Funnel\Commands\StartFunnelCommand;
use App\Application\Funnel\StartFunnelHandler;
use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\Services\FunnelStarter;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StartFunnelHandlerTest extends TestCase
{
    use RefreshDatabase;

    private StartFunnelHandler $handler;

    private FunnelStarter $funnelStarter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->funnelStarter = Mockery::mock(FunnelStarter::class);
        $this->handler = new StartFunnelHandler($this->funnelStarter);
    }

    public function test_returns_subscriber_not_found_when_subscriber_does_not_exist(): void
    {
        $command = new StartFunnelCommand(
            trigger: Funnel::TRIGGER_CONTRACT_CREATED,
            subscriberId: 999,
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Subscriber not found', $result->error);
    }

    public function test_returns_empty_runs_when_no_funnels_triggered(): void
    {
        $subscriber = Subscriber::factory()->create();

        $this->funnelStarter
            ->shouldReceive('startByTrigger')
            ->with(Funnel::TRIGGER_CONTRACT_CREATED, Mockery::on(fn ($s) => $s->id === $subscriber->id))
            ->once()
            ->andReturn([]);

        $command = new StartFunnelCommand(
            trigger: Funnel::TRIGGER_CONTRACT_CREATED,
            subscriberId: $subscriber->id,
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->startedRunIds);
    }

    public function test_returns_started_run_ids_when_funnels_triggered(): void
    {
        $subscriber = Subscriber::factory()->create();

        $run1 = new \stdClass();
        $run1->id = 1;

        $run2 = new \stdClass();
        $run2->id = 2;

        $this->funnelStarter
            ->shouldReceive('startByTrigger')
            ->with(Funnel::TRIGGER_ORDER_PAID, Mockery::on(fn ($s) => $s->id === $subscriber->id))
            ->once()
            ->andReturn([$run1, $run2]);

        $command = new StartFunnelCommand(
            trigger: Funnel::TRIGGER_ORDER_PAID,
            subscriberId: $subscriber->id,
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals([1, 2], $result->startedRunIds);
    }
}
