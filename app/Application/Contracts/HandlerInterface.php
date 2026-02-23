<?php

namespace App\Application\Contracts;

/**
 * Handles a command and returns a normalized result.
 */
interface HandlerInterface
{
    /**
     * Execute the use-case for the given command.
     *
     * @param CommandInterface $command Use-case input payload.
     * @return ResultInterface Normalized use-case output.
     */
    public function handle(CommandInterface $command): ResultInterface;
}
