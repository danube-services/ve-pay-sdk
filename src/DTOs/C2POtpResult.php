<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

readonly class C2POtpResult
{
    public function __construct(
        public bool $success,
        public string $message = '',
        public array $rawResponse = [],
    ) {
    }
}
