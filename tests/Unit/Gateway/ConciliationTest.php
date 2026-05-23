<?php

declare(strict_types=1);

use Danube\VePaySdk\Contracts\PaymentValidator;
use Danube\VePaySdk\DTOs\PaymentStatus;
use Danube\VePaySdk\DTOs\PaymentValidationRequest;
use Danube\VePaySdk\DTOs\PaymentValidationResult;
use Danube\VePaySdk\Gateway\Conciliation;

test('Conciliation conciliate delegates to driver validate', function () {
    $expectedResult = new PaymentValidationResult(
        status: PaymentStatus::Matched,
        amount: '120.00',
        reference: '12345678',
        message: 'OK',
    );

    $driver = new class ($expectedResult) implements PaymentValidator {
        public bool $called = false;

        public function __construct(
            private PaymentValidationResult $result,
        ) {
        }

        public function validate(PaymentValidationRequest $request): PaymentValidationResult
        {
            $this->called = true;
            return $this->result;
        }
    };

    $handler = new Conciliation($driver);

    $request = new PaymentValidationRequest(
        reference: '12345678',
        amount: 120.00,
        date: new DateTimeImmutable('2023-02-12'),
    );

    $result = $handler->conciliate($request);

    expect($driver->called)->toBeTrue()
        ->and($result->status)->toBe(PaymentStatus::Matched)
        ->and($result->amount)->toBe('120.00')
        ->and($result->reference)->toBe('12345678')
        ->and($result->message)->toBe('OK');
});

test('Conciliation conciliate returns whatever driver returns', function () {
    $expectedResult = new PaymentValidationResult(
        status: PaymentStatus::NotMatched,
        message: 'No encontrado',
    );

    $driver = new readonly class ($expectedResult) implements PaymentValidator {
        public function __construct(
            private PaymentValidationResult $result,
        ) {
        }

        public function validate(PaymentValidationRequest $request): PaymentValidationResult
        {
            return $this->result;
        }
    };

    $handler = new Conciliation($driver);

    $request = new PaymentValidationRequest(
        reference: '99999999',
        amount: 50.00,
        date: new DateTimeImmutable('2024-06-01'),
    );

    $result = $handler->conciliate($request);

    expect($result->status)->toBe(PaymentStatus::NotMatched)
        ->and($result->message)->toBe('No encontrado');
});
