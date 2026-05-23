<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class C2POtpRequest
{
    public function __construct(
        public string $customerDocumentId,
        public ?string $customerPhone = null,
        public array $metadata = [],
    ) {
    }
}
