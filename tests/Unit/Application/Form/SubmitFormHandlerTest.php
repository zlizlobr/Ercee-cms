<?php

namespace Tests\Unit\Application\Form;

use Modules\Forms\Application\Commands\SubmitFormCommand;
use Modules\Forms\Application\SubmitFormHandler;
use Modules\Forms\Domain\Contract;
use Modules\Forms\Domain\Events\ContractCreated;
use Modules\Forms\Domain\Form;
use Modules\Forms\Domain\Subscriber\Subscriber;
use Modules\Forms\Domain\Subscriber\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class SubmitFormHandlerTest extends TestCase
{
    use RefreshDatabase;

    private SubmitFormHandler $handler;

    private SubscriberService $subscriberService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberService = Mockery::mock(SubscriberService::class);
        $this->handler = new SubmitFormHandler($this->subscriberService);
    }

    public function test_returns_form_not_found_when_form_does_not_exist(): void
    {
        $command = new SubmitFormCommand(
            formId: 999,
            email: 'test@example.com',
            data: ['name' => 'Test'],
            source: 'test',
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Form not found', $result->error);
    }

    public function test_returns_honeypot_result_when_honeypot_is_filled(): void
    {
        $form = Form::factory()->create(['active' => true]);

        $command = new SubmitFormCommand(
            formId: $form->id,
            email: 'test@example.com',
            data: ['name' => 'Test'],
            source: 'test',
            isHoneypotFilled: true,
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertTrue($result->isHoneypot());
        $this->assertNull($result->contractId);
    }

    public function test_returns_validation_failed_when_data_is_invalid(): void
    {
        $form = Form::factory()->create([
            'active' => true,
            'schema' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
            ],
        ]);

        $command = new SubmitFormCommand(
            formId: $form->id,
            email: 'invalid-email',
            data: [],
            source: 'test',
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Validation failed', $result->error);
        $this->assertNotEmpty($result->validationErrors);
    }

    public function test_creates_contract_and_dispatches_event_on_success(): void
    {
        Event::fake([ContractCreated::class]);

        $form = Form::factory()->create([
            'active' => true,
            'schema' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
            ],
        ]);

        $subscriber = Subscriber::factory()->create();

        $this->subscriberService
            ->shouldReceive('findOrCreateByEmail')
            ->with('test@example.com', 'test-source')
            ->once()
            ->andReturn($subscriber);

        $command = new SubmitFormCommand(
            formId: $form->id,
            email: 'test@example.com',
            data: ['name' => 'John Doe'],
            source: 'test-source',
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertNotNull($result->contractId);

        $contract = Contract::find($result->contractId);
        $this->assertNotNull($contract);
        $this->assertEquals($subscriber->id, $contract->subscriber_id);
        $this->assertEquals($form->id, $contract->form_id);
        $this->assertEquals('test@example.com', $contract->email);
        $this->assertEquals(['name' => 'John Doe'], $contract->data);

        Event::assertDispatched(ContractCreated::class);
    }
}
