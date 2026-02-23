<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Llm\Services\LlmManager;
use Modules\Llm\Domain\Prompt;
use Modules\Llm\Domain\Exceptions\LlmException;

/**
 * Demonstrate non-streaming and streaming LLM calls from CLI.
 */
class LlmExampleCommand extends Command
{
    protected $signature = 'llm:example
                          {prompt : The prompt to send to the LLM}
                          {--provider= : Preferred provider (openai|claude|gemini)}
                          {--model= : Model name}
                          {--stream : Enable streaming}
                          {--temperature=0.7 : Temperature (0.0-1.0)}
                          {--max-tokens=500 : Maximum tokens}';

    protected $description = 'Example command demonstrating LLM usage';

    /**
     * Handle the command execution.
     *
     * @return int Exit code (`Command::SUCCESS` on success, `Command::FAILURE` on provider error).
     */
    public function handle(LlmManager $manager): int
    {
        $userPrompt = $this->argument('prompt');

        $prompt = new Prompt(
            userPrompt: $userPrompt,
            systemPrompt: 'You are a helpful AI assistant integrated into Ercee CMS.',
            model: $this->option('model'),
            maxTokens: (int) $this->option('max-tokens'),
            temperature: (float) $this->option('temperature'),
            preferredProvider: $this->option('provider'),
            streaming: $this->option('stream'),
        );

        $this->info("Sending prompt to LLM...\n");

        try {
            if ($this->option('stream')) {
                $this->handleStreaming($manager, $prompt);
            } else {
                $this->handleComplete($manager, $prompt);
            }

            return self::SUCCESS;
        } catch (LlmException $e) {
            $this->error("LLM Error: {$e->getMessage()}");
            $this->error("Error Type: {$e->getErrorType()->value}");

            if ($e->getProvider()) {
                $this->error("Provider: {$e->getProvider()}");
            }

            return self::FAILURE;
        }
    }

    /**
     * Run a non-streaming completion request.
     *
     * @param LlmManager $manager LLM manager instance.
     * @param Prompt $prompt Prompt payload for completion.
     * @return void
     */
    protected function handleComplete(LlmManager $manager, Prompt $prompt): void
    {
        $response = $manager->complete($prompt);

        $this->line("─────────────────────────────────────");
        $this->line($response->getContent());
        $this->line("─────────────────────────────────────\n");

        $this->comment("Provider: {$response->getProvider()}");
        $this->comment("Model: {$response->getModel()}");
        $this->comment("Finish Reason: {$response->getFinishReason()->value}");
        $this->comment("Tokens Used: {$response->getUsage()->totalTokens}");
        $this->comment("  ├─ Input: {$response->getUsage()->inputTokens}");
        $this->comment("  └─ Output: {$response->getUsage()->outputTokens}");
    }

    /**
     * Run a streaming completion request.
     *
     * @param LlmManager $manager LLM manager instance.
     * @param Prompt $prompt Prompt payload for streaming.
     * @return void
     */
    protected function handleStreaming(LlmManager $manager, Prompt $prompt): void
    {
        $stream = $manager->stream($prompt);

        $this->line("─────────────────────────────────────");

        foreach ($stream as $chunk) {
            if ($chunk->getType() === 'content') {
                $this->getOutput()->write($chunk->getContent());
            }

            if ($chunk->isComplete()) {
                $this->newLine();
                $this->line("─────────────────────────────────────");
                $this->comment("\n[Stream completed]");
            }
        }
    }
}

