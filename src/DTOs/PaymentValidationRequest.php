<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class PaymentValidationRequest
{
    public function __construct(
        public string $reference,
        public string $amount,
        public string $date,
        public ?string $payerPhone = null,
        public ?string $destPhone = null,
        public ?string $bankCode = null,
        public array $metadata = [],
    ) {
    }
}
