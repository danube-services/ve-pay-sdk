<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class C2PAnnulRequest
{
    public function __construct(
        public ?string $transactionId = null,
        public ?string $amount = null,
        public ?string $terminal = null,
        public ?string $debtorDoc = null,
        public ?string $debtorPhone = null,
        public ?string $bankCode = null,
        public array $metadata = [],
    ) {
    }
}
