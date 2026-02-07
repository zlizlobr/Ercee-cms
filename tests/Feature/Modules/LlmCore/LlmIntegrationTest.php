<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\LlmCore;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Modules\Llm\Services\LlmManager;
use Modules\Llm\Services\ProviderResolver;
use Modules\Llm\Domain\Prompt;
use Modules\Llm\Domain\Response;
use Modules\Llm\Domain\ValueObjects\FinishReason;
use Modules\Llm\Domain\ValueObjects\Usage;
use Modules\Llm\Domain\Contracts\LlmClientInterface;
use Modules\Llm\Domain\Events\PromptSent;
use Modules\Llm\Domain\Events\ResponseReceived;

class LlmIntegrationTest extends TestCase
{
    public function test_complete_flow_with_mock_provider(): void
    {
        Event::fake();

        $resolver = app(ProviderResolver::class);
        $manager = app(LlmManager::class);

        $mockProvider = Mockery::mock(LlmClientInterface::class);
        $mockProvider->shouldReceive('getName')->andReturn('mock');
        $mockProvider->shouldReceive('supports')->andReturn(true);
        $mockProvider->shouldReceive('complete')->andReturn(
            new Response(
                content: 'This is a mock response',
                finishReason: FinishReason::STOP,
                usage: new Usage(inputTokens: 5, outputTokens: 10, totalTokens: 15),
                provider: 'mock',
                model: 'mock-model',
            )
        );

        $resolver->registerProvider('mock', $mockProvider);

        config(['module.llm.default_provider' => 'mock']);
        config(['module.llm.fallback_providers' => []]);

        $prompt = new Prompt(
            userPrompt: 'Test prompt',
            systemPrompt: 'You are a helpful assistant',
            maxTokens: 100,
            temperature: 0.7,
        );

        $response = $manager->complete($prompt);

        $this->assertEquals('This is a mock response', $response->getContent());
        $this->assertEquals('mock', $response->getProvider());
        $this->assertEquals('mock-model', $response->getModel());
        $this->assertEquals(FinishReason::STOP, $response->getFinishReason());
        $this->assertEquals(15, $response->getUsage()->totalTokens);

        Event::assertDispatched(PromptSent::class, function ($event) {
            return $event->provider === 'mock';
        });

        Event::assertDispatched(ResponseReceived::class, function ($event) {
            return $event->provider === 'mock'
                && $event->usage->totalTokens === 15;
        });
    }

    public function test_provider_preference_is_respected(): void
    {
        $resolver = app(ProviderResolver::class);
        $manager = app(LlmManager::class);

        $provider1 = $this->createMockProvider('provider1', 'Response from provider 1');
        $provider2 = $this->createMockProvider('provider2', 'Response from provider 2');

        $resolver->registerProvider('provider1', $provider1);
        $resolver->registerProvider('provider2', $provider2);

        config(['module.llm.default_provider' => 'provider1']);

        $prompt = new Prompt(
            userPrompt: 'Test',
            preferredProvider: 'provider2',
        );

        $response = $manager->complete($prompt);

        $this->assertEquals('Response from provider 2', $response->getContent());
        $this->assertEquals('provider2', $response->getProvider());
    }

    public function test_allowed_providers_restriction_works(): void
    {
        $resolver = app(ProviderResolver::class);
        $manager = app(LlmManager::class);

        $provider1 = $this->createMockProvider('provider1', 'Response 1');
        $provider2 = $this->createMockProvider('provider2', 'Response 2');
        $provider3 = $this->createMockProvider('provider3', 'Response 3');

        $resolver->registerProvider('provider1', $provider1);
        $resolver->registerProvider('provider2', $provider2);
        $resolver->registerProvider('provider3', $provider3);

        config(['module.llm.default_provider' => 'provider1']);
        config(['module.llm.fallback_providers' => []]);

        $prompt = new Prompt(
            userPrompt: 'Test',
            allowedProviders: ['provider2', 'provider3'],
        );

        $response = $manager->complete($prompt);

        $this->assertContains($response->getProvider(), ['provider2', 'provider3']);
        $this->assertNotEquals('provider1', $response->getProvider());
    }

    protected function createMockProvider(string $name, string $responseContent): LlmClientInterface
    {
        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn($name);
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('complete')->andReturn(
            new Response(
                content: $responseContent,
                finishReason: FinishReason::STOP,
                usage: new Usage(5, 10, 15),
                provider: $name,
                model: "{$name}-model",
            )
        );

        return $provider;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
