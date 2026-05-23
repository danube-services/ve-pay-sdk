<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

use DateTimeImmutable;

readonly class PaymentValidationRequest
{
    public function __construct(
        public string $reference,
        public float $amount,
        public DateTimeImmutable $date,
        public ?string $payerPhone = null,
        public ?string $destPhone = null,
        public ?string $bankCode = null,
        public ?Document $payerDoc = null,
        public array $metadata = [],
    ) {
    }
}
