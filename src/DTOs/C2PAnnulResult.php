<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class C2PAnnulResult
{
    public function __construct(
        public bool $success,
        public ?string $transactionId = null,
        public ?string $reference = null,
        public string $message = '',
        public array $rawResponse = [],
    ) {
    }
}
