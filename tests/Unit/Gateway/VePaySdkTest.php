<?php

declare(strict_types=1);

use Danube\VePaySdk\Contracts\PaymentValidator;
use Danube\VePaySdk\DTOs\PaymentStatus;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;
use Danube\VePaySdk\Gateway\Conciliation;
use Danube\VePaySdk\Gateway\VePaySdk;

test('VePaySdk exposes conciliation property', function () {
    $driver = new readonly class () implements PaymentValidator {
        public function validate(PaymentValidationRequest $request): PaymentValidationResult
        {
            return new PaymentValidationResult(status: PaymentStatus::NotMatched);
        }
    };

    $sdk = new VePaySdk($driver);

    expect($sdk->conciliation)->toBeInstanceOf(Conciliation::class);
});
