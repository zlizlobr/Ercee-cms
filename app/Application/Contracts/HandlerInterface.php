<?php

namespace App\Application\Contracts;

interface HandlerInterface
{
    public function handle(CommandInterface $command): ResultInterface;
}
