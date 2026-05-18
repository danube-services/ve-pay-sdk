<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Contracts;

use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;

interface PaymentValidator
{
    public function validate(PaymentValidationRequest $request): PaymentValidationResult;
}
