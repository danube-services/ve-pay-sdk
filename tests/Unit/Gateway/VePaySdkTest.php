<?php

declare(strict_types=1);

use Danube\VePaySdk\Contracts\C2PPaymentDriver;
use Danube\VePaySdk\Contracts\PaymentValidator;
use Danube\VePaySdk\DTOs\C2PAnnulRequest;
use Danube\VePaySdk\DTOs\C2PAnnulResult;
use Danube\VePaySdk\DTOs\C2POtpRequest;
use Danube\VePaySdk\DTOs\C2POtpResult;
use Danube\VePaySdk\DTOs\C2PProcessRequest;
use Danube\VePaySdk\DTOs\C2PProcessResult;
use Danube\VePaySdk\DTOs\PaymentStatus;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;
use Danube\VePaySdk\Gateway\C2PPayment;
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

test('VePaySdk c2p is null when no C2P driver provided', function () {
    $driver = new readonly class () implements PaymentValidator {
        public function validate(PaymentValidationRequest $request): PaymentValidationResult
        {
            return new PaymentValidationResult(status: PaymentStatus::NotMatched);
        }
    };

    $sdk = new VePaySdk($driver);

    expect($sdk->c2p)->toBeNull();
});

test('VePaySdk exposes c2p property when C2P driver provided', function () {
    $conciliationDriver = new readonly class () implements PaymentValidator {
        public function validate(PaymentValidationRequest $request): PaymentValidationResult
        {
            return new PaymentValidationResult(status: PaymentStatus::NotMatched);
        }
    };

    $c2pDriver = new readonly class () implements C2PPaymentDriver {
        public function requestOtp(C2POtpRequest $request): C2POtpResult
        {
            return new C2POtpResult(success: false);
        }
        public function pay(C2PProcessRequest $request): C2PProcessResult
        {
            return new C2PProcessResult(success: false);
        }
        public function annul(C2PAnnulRequest $request): C2PAnnulResult
        {
            return new C2PAnnulResult(success: false);
        }
    };

    $sdk = new VePaySdk($conciliationDriver, $c2pDriver);

    expect($sdk->c2p)->toBeInstanceOf(C2PPayment::class);
});
