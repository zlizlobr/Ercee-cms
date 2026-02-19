<?php

namespace Tests\Feature;

use Modules\Forms\Domain\Contract;
use App\Events\ContractCreated;
use Modules\Forms\Domain\Form;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_form_successfully(): void
    {
        Event::fake([ContractCreated::class]);

        $form = Form::factory()->create([
            'active' => true,
            'schema' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'message', 'type' => 'textarea', 'label' => 'Message', 'required' => false],
            ],
        ]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'message' => 'Hello world',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['contract_id'],
            ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('contracts', [
            'form_id' => $form->id,
            'email' => 'john@example.com',
            'status' => Contract::STATUS_NEW,
        ]);

        Event::assertDispatched(ContractCreated::class);
    }

    public function test_returns_404_for_inactive_form(): void
    {
        $form = Form::factory()->create(['active' => false]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Form not found']);
    }

    public function test_returns_422_for_invalid_data(): void
    {
        $form = Form::factory()->create([
            'active' => true,
            'schema' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
            ],
        ]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Validation failed']);
    }

    public function test_honeypot_returns_success_without_creating_contract(): void
    {
        $form = Form::factory()->create([
            'active' => true,
            'schema' => [],
        ]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'bot@spam.com',
            '_hp_field' => 'spam content',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Thank you for your submission.']);

        $this->assertDatabaseMissing('contracts', [
            'email' => 'bot@spam.com',
        ]);
    }

    public function test_uses_existing_subscriber_if_email_exists(): void
    {
        Event::fake([ContractCreated::class]);

        $existingSubscriber = Subscriber::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $form = Form::factory()->create([
            'active' => true,
            'schema' => [],
        ]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseCount('subscribers', 1);

        $contract = Contract::first();
        $this->assertEquals($existingSubscriber->id, $contract->subscriber_id);
    }
}
