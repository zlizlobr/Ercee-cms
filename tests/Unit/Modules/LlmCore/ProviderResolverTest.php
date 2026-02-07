<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\LlmCore;

use Mockery;
use Tests\TestCase;
use Modules\LlmCore\Services\ProviderResolver;
use Modules\LlmCore\Domain\Contracts\LlmClientInterface;
use Modules\LlmCore\Domain\Contracts\LlmPromptInterface;
use Modules\LlmCore\Domain\Exceptions\ProviderNotFoundException;
use Modules\LlmCore\Domain\Exceptions\ModelNotSupportedException;

class ProviderResolverTest extends TestCase
{
    protected ProviderResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ProviderResolver();
    }

    public function test_registers_provider(): void
    {
        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn('test-provider');

        $this->resolver->registerProvider('test-provider', $provider);

        $this->assertSame($provider, $this->resolver->getProvider('test-provider'));
    }

    public function test_throws_exception_for_unknown_provider(): void
    {
        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("LLM provider 'unknown' not found or not registered");

        $this->resolver->getProvider('unknown');
    }

    public function test_resolves_preferred_provider_when_available(): void
    {
        $provider1 = $this->createMockProvider('provider1', ['model-a']);
        $provider2 = $this->createMockProvider('provider2', ['model-b']);

        $this->resolver->registerProvider('provider1', $provider1);
        $this->resolver->registerProvider('provider2', $provider2);

        $prompt = $this->createMockPrompt(
            model: 'model-a',
            preferredProvider: 'provider1'
        );

        $resolved = $this->resolver->resolveProvider($prompt, 'provider2', []);

        $this->assertSame($provider1, $resolved);
    }

    public function test_falls_back_to_default_provider_when_preferred_not_available(): void
    {
        $provider1 = $this->createMockProvider('provider1', ['model-a']);
        $provider2 = $this->createMockProvider('provider2', ['model-a']);

        $this->resolver->registerProvider('provider1', $provider1);
        $this->resolver->registerProvider('provider2', $provider2);

        $prompt = $this->createMockPrompt(
            model: 'model-a',
            preferredProvider: 'non-existent'
        );

        $resolved = $this->resolver->resolveProvider($prompt, 'provider2', []);

        $this->assertSame($provider2, $resolved);
    }

    public function test_uses_fallback_providers_when_default_does_not_support_model(): void
    {
        $provider1 = $this->createMockProvider('provider1', ['model-a']);
        $provider2 = $this->createMockProvider('provider2', ['model-b']);
        $provider3 = $this->createMockProvider('provider3', ['model-c']);

        $this->resolver->registerProvider('provider1', $provider1);
        $this->resolver->registerProvider('provider2', $provider2);
        $this->resolver->registerProvider('provider3', $provider3);

        $prompt = $this->createMockPrompt(model: 'model-c');

        $resolved = $this->resolver->resolveProvider(
            $prompt,
            'provider1',
            ['provider2', 'provider3']
        );

        $this->assertSame($provider3, $resolved);
    }

    public function test_respects_allowed_providers_restriction(): void
    {
        $provider1 = $this->createMockProvider('provider1', ['model-a']);
        $provider2 = $this->createMockProvider('provider2', ['model-a']);

        $this->resolver->registerProvider('provider1', $provider1);
        $this->resolver->registerProvider('provider2', $provider2);

        $prompt = $this->createMockPrompt(
            model: 'model-a',
            allowedProviders: ['provider2']
        );

        $resolved = $this->resolver->resolveProvider($prompt, 'provider1', []);

        $this->assertSame($provider2, $resolved);
    }

    public function test_throws_exception_when_no_provider_supports_model(): void
    {
        $provider1 = $this->createMockProvider('provider1', ['model-a']);
        $provider2 = $this->createMockProvider('provider2', ['model-b']);

        $this->resolver->registerProvider('provider1', $provider1);
        $this->resolver->registerProvider('provider2', $provider2);

        $prompt = $this->createMockPrompt(model: 'model-c');

        $this->expectException(ModelNotSupportedException::class);

        $this->resolver->resolveProvider($prompt, 'provider1', ['provider2']);
    }

    protected function createMockProvider(string $name, array $supportedModels): LlmClientInterface
    {
        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn($name);
        $provider->shouldReceive('supports')
            ->andReturnUsing(fn($model) => in_array($model, $supportedModels, true));

        return $provider;
    }

    protected function createMockPrompt(
        ?string $model = null,
        ?string $preferredProvider = null,
        ?array $allowedProviders = null
    ): LlmPromptInterface {
        $prompt = Mockery::mock(LlmPromptInterface::class);
        $prompt->shouldReceive('getModel')->andReturn($model);
        $prompt->shouldReceive('getPreferredProvider')->andReturn($preferredProvider);
        $prompt->shouldReceive('getAllowedProviders')->andReturn($allowedProviders);

        return $prompt;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
