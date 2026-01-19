<?php

namespace App\Application\Commerce\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class CreateOrderCommand implements CommandInterface
{
    public function __construct(
        public int $productId,
        public string $email,
    ) {}

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'email' => $this->email,
        ];
    }
}
