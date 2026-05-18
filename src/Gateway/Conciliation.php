<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Gateway;

use Danube\VePaySdk\Contracts\PaymentValidator;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;

readonly class Conciliation
{
    public function __construct(
        private PaymentValidator $driver,
    ) {
    }

    public function conciliate(PaymentValidationRequest $request): PaymentValidationResult
    {
        return $this->driver->validate($request);
    }
}
