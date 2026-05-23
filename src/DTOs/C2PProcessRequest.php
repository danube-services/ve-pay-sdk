<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class C2PProcessRequest
{
    public function __construct(
        public string $customerDocumentId,
        public string $customerPhone,
        public string $amount,
        public string $customerBankCode,
        public string $otp,
        public ?string $concept = null,
        public ?string $coinType = null,
        public ?string $commercePhone = null,
        public array $metadata = [],
    ) {
    }
}
