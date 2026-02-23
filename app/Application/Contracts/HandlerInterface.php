<?php

namespace App\Application\Contracts;

/**
 * Handles a command and returns a normalized result.
 */
interface HandlerInterface
{
    /**
     * Execute the use-case for the given command.
     */
    public function handle(CommandInterface $command): ResultInterface;
}
