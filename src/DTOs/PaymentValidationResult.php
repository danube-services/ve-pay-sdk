<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class PaymentValidationResult
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $amount = null,
        public ?string $reference = null,
        public ?string $date = null,
        public ?string $debtorId = null,
        public ?string $debtorType = null,
        public string $message = '',
        public array $rawResponse = [],
    ) {
    }
}
