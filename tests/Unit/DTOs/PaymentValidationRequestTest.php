<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\PaymentValidationRequest;

test('PaymentValidationRequest sets required fields', function () {
    $request = new PaymentValidationRequest(
        reference: '12345678',
        amount: '120.00',
        date: '2023-02-12',
    );

    expect($request->reference)->toBe('12345678')
        ->and($request->amount)->toBe('120.00')
        ->and($request->date)->toBe('2023-02-12');
});

test('PaymentValidationRequest optional fields default to null', function () {
    $request = new PaymentValidationRequest(
        reference: '12345678',
        amount: '120.00',
        date: '2023-02-12',
    );

    expect($request->payerPhone)->toBeNull()
        ->and($request->destPhone)->toBeNull()
        ->and($request->bankCode)->toBeNull();
});

test('PaymentValidationRequest metadata defaults to empty array', function () {
    $request = new PaymentValidationRequest(
        reference: '12345678',
        amount: '120.00',
        date: '2023-02-12',
    );

    expect($request->metadata)->toBe([]);
});

test('PaymentValidationRequest accepts all fields', function () {
    $request = new PaymentValidationRequest(
        reference: '87654321',
        amount: '250.50',
        date: '2024-01-15',
        payerPhone: '584241234567',
        destPhone: '584121234567',
        bankCode: '0102',
        metadata: ['reqCed' => true, 'cedulaPagador' => 'V27037606'],
    );

    expect($request->reference)->toBe('87654321')
        ->and($request->amount)->toBe('250.50')
        ->and($request->date)->toBe('2024-01-15')
        ->and($request->payerPhone)->toBe('584241234567')
        ->and($request->destPhone)->toBe('584121234567')
        ->and($request->bankCode)->toBe('0102')
        ->and($request->metadata)->toBe(['reqCed' => true, 'cedulaPagador' => 'V27037606']);
});

test('PaymentValidationRequest is readonly', function () {
    $request = new PaymentValidationRequest(
        reference: '12345678',
        amount: '120.00',
        date: '2023-02-12',
    );

    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
