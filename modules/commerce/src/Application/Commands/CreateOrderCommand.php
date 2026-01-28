<?php

namespace Modules\Commerce\Application\Commands;

use App\Application\Contracts\CommandInterface;

readonly class CreateOrderCommand implements CommandInterface
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
