<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\LlmCore;

use Generator;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Modules\Llm\Services\LlmManager;
use Modules\Llm\Services\ProviderResolver;
use Modules\Llm\Domain\Contracts\LlmClientInterface;
use Modules\Llm\Domain\Contracts\LlmPromptInterface;
use Modules\Llm\Domain\Response;
use Modules\Llm\Domain\StreamChunk;
use Modules\Llm\Domain\Prompt;
use Modules\Llm\Domain\ValueObjects\FinishReason;
use Modules\Llm\Domain\ValueObjects\Usage;
use Modules\Llm\Domain\ValueObjects\ErrorType;
use Modules\Llm\Domain\Events\PromptSent;
use Modules\Llm\Domain\Events\ResponseReceived;
use Modules\Llm\Domain\Events\ProviderFailed;
use Modules\Llm\Domain\Exceptions\LlmException;

class LlmManagerTest extends TestCase
{
    protected LlmManager $manager;
    protected ProviderResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new ProviderResolver();
        $this->manager = new LlmManager($this->resolver);
    }

    public function test_completes_prompt_successfully(): void
    {
        Event::fake([PromptSent::class, ResponseReceived::class]);

        $expectedResponse = new Response(
            content: 'Test response',
            finishReason: FinishReason::STOP,
            usage: new Usage(10, 20, 30),
            provider: 'test-provider',
            model: 'test-model',
        );

        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn('test-provider');
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('complete')->once()->andReturn($expectedResponse);

        $this->resolver->registerProvider('test-provider', $provider);

        $prompt = new Prompt(userPrompt: 'Test prompt');

        config(['module.llm.default_provider' => 'test-provider']);
        config(['module.llm.fallback_providers' => []]);

        $response = $this->manager->complete($prompt);

        $this->assertEquals('Test response', $response->getContent());
        $this->assertEquals('test-provider', $response->getProvider());

        Event::assertDispatched(PromptSent::class);
        Event::assertDispatched(ResponseReceived::class);
    }

    public function test_dispatches_failure_event_on_error(): void
    {
        Event::fake([ProviderFailed::class]);

        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn('test-provider');
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('complete')->once()->andThrow(
            new LlmException('Test error', ErrorType::USER, 'test-provider')
        );

        $this->resolver->registerProvider('test-provider', $provider);

        $prompt = new Prompt(userPrompt: 'Test prompt');

        config(['module.llm.default_provider' => 'test-provider']);
        config(['module.llm.fallback_providers' => []]);

        try {
            $this->manager->complete($prompt);
            $this->fail('Expected LlmException was not thrown');
        } catch (LlmException $e) {
            Event::assertDispatched(ProviderFailed::class);
        }
    }

    public function test_attempts_fallback_on_transient_error(): void
    {
        $fallbackResponse = new Response(
            content: 'Fallback response',
            finishReason: FinishReason::STOP,
            usage: new Usage(10, 20, 30),
            provider: 'fallback-provider',
            model: 'fallback-model',
        );

        $primaryProvider = Mockery::mock(LlmClientInterface::class);
        $primaryProvider->shouldReceive('getName')->andReturn('primary-provider');
        $primaryProvider->shouldReceive('supports')->andReturn(true);
        $primaryProvider->shouldReceive('complete')->once()->andThrow(
            new LlmException('Rate limit', ErrorType::TRANSIENT, 'primary-provider')
        );

        $fallbackProvider = Mockery::mock(LlmClientInterface::class);
        $fallbackProvider->shouldReceive('getName')->andReturn('fallback-provider');
        $fallbackProvider->shouldReceive('supports')->andReturn(true);
        $fallbackProvider->shouldReceive('complete')->once()->andReturn($fallbackResponse);

        $this->resolver->registerProvider('primary-provider', $primaryProvider);
        $this->resolver->registerProvider('fallback-provider', $fallbackProvider);

        $prompt = new Prompt(userPrompt: 'Test prompt');

        config(['module.llm.default_provider' => 'primary-provider']);
        config(['module.llm.fallback_providers' => ['fallback-provider']]);

        $response = $this->manager->complete($prompt);

        $this->assertEquals('Fallback response', $response->getContent());
        $this->assertEquals('fallback-provider', $response->getProvider());
    }

    public function test_does_not_attempt_fallback_on_user_error(): void
    {
        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn('test-provider');
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('complete')->once()->andThrow(
            new LlmException('Invalid request', ErrorType::USER, 'test-provider')
        );

        $this->resolver->registerProvider('test-provider', $provider);

        $prompt = new Prompt(userPrompt: 'Test prompt');

        config(['module.llm.default_provider' => 'test-provider']);
        config(['module.llm.fallback_providers' => ['other-provider']]);

        $this->expectException(LlmException::class);
        $this->expectExceptionMessage('Invalid request');

        $this->manager->complete($prompt);
    }

    public function test_streams_response(): void
    {
        $provider = Mockery::mock(LlmClientInterface::class);
        $provider->shouldReceive('getName')->andReturn('test-provider');
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('stream')->once()->andReturn($this->createMockStream());

        $this->resolver->registerProvider('test-provider', $provider);

        $prompt = new Prompt(userPrompt: 'Test prompt', streaming: true);

        config(['module.llm.default_provider' => 'test-provider']);
        config(['module.llm.fallback_providers' => []]);

        $stream = $this->manager->stream($prompt);

        $chunks = iterator_to_array($stream);

        $this->assertCount(3, $chunks);
        $this->assertEquals('chunk1', $chunks[0]->getContent());
        $this->assertEquals('chunk2', $chunks[1]->getContent());
        $this->assertTrue($chunks[2]->isComplete());
    }

    protected function createMockStream(): Generator
    {
        yield new StreamChunk('content', 'chunk1', [], false);
        yield new StreamChunk('content', 'chunk2', [], false);
        yield new StreamChunk('complete', '', [], true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
