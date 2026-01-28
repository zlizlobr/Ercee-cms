<?php

namespace Modules\Forms\Application\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class SubmitFormCommand implements CommandInterface
{
    public function __construct(
        public int $formId,
        public string $email,
        public array $data,
        public string $source,
        public bool $isHoneypotFilled = false,
    ) {}

    public function toArray(): array
    {
        return [
            'form_id' => $this->formId,
            'email' => $this->email,
            'data' => $this->data,
            'source' => $this->source,
        ];
    }
}
