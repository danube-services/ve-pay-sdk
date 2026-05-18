<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\PaymentStatus;
use Danube\VePaySdk\DTOs\PaymentValidationResult;

test('PaymentValidationResult sets all properties correctly', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::Matched,
        amount: '120.00',
        reference: '12345678',
        date: '2023-02-12',
        debtorId: 'V27037606',
        debtorType: 'V',
        message: 'Transacción realizada',
        rawResponse: ['code' => 1000, 'data' => ['status' => '1000']],
    );

    expect($result->status)->toBe(PaymentStatus::Matched)
        ->and($result->amount)->toBe('120.00')
        ->and($result->reference)->toBe('12345678')
        ->and($result->date)->toBe('2023-02-12')
        ->and($result->debtorId)->toBe('V27037606')
        ->and($result->debtorType)->toBe('V')
        ->and($result->message)->toBe('Transacción realizada')
        ->and($result->rawResponse)->toBe(['code' => 1000, 'data' => ['status' => '1000']]);
});

test('PaymentValidationResult nullable fields default to null', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::NotMatched,
    );

    expect($result->amount)->toBeNull()
        ->and($result->reference)->toBeNull()
        ->and($result->date)->toBeNull()
        ->and($result->debtorId)->toBeNull()
        ->and($result->debtorType)->toBeNull();
});

test('PaymentValidationResult message defaults to empty string', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::Error,
    );

    expect($result->message)->toBe('');
});

test('PaymentValidationResult rawResponse defaults to empty array', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::NotMatched,
    );

    expect($result->rawResponse)->toBe([]);
});

test('PaymentValidationResult is readonly', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::Matched,
    );

    $reflection = new ReflectionClass($result);

    expect($reflection->isReadOnly())->toBeTrue();
});

test('PaymentValidationResult with AlreadyReconciled status', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::AlreadyReconciled,
        message: 'Pago ya conciliado anteriormente',
    );

    expect($result->status)->toBe(PaymentStatus::AlreadyReconciled)
        ->and($result->message)->toBe('Pago ya conciliado anteriormente');
});

test('PaymentValidationResult with Error status', function () {
    $result = new PaymentValidationResult(
        status: PaymentStatus::Error,
        message: 'No se pudo validar el movimiento',
    );

    expect($result->status)->toBe(PaymentStatus::Error)
        ->and($result->message)->toBe('No se pudo validar el movimiento');
});
