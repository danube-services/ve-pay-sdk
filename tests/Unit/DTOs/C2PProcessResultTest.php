<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2PProcessResult;

test('C2PProcessResult sets all properties correctly', function () {
    $result = new C2PProcessResult(
        success: true,
        transactionId: '01020102984000790909404165892642',
        reference: '090037579602',
        date: '2025-11-14',
        message: 'Pago procesado exitosamente',
        rawResponse: ['code' => '1000'],
    );

    expect($result->success)->toBeTrue()
        ->and($result->transactionId)->toBe('01020102984000790909404165892642')
        ->and($result->reference)->toBe('090037579602')
        ->and($result->date)->toBe('2025-11-14')
        ->and($result->message)->toBe('Pago procesado exitosamente')
        ->and($result->rawResponse)->toBe(['code' => '1000']);
});

test('C2PProcessResult nullable fields default to null', function () {
    $result = new C2PProcessResult(success: true);

    expect($result->transactionId)->toBeNull()
        ->and($result->reference)->toBeNull()
        ->and($result->date)->toBeNull();
});

test('C2PProcessResult message defaults to empty string', function () {
    $result = new C2PProcessResult(success: false);

    expect($result->message)->toBe('');
});

test('C2PProcessResult rawResponse defaults to empty array', function () {
    $result = new C2PProcessResult(success: false);

    expect($result->rawResponse)->toBe([]);
});

test('C2PProcessResult failure state', function () {
    $result = new C2PProcessResult(
        success: false,
        message: 'Fondos insuficientes',
    );

    expect($result->success)->toBeFalse()
        ->and($result->message)->toBe('Fondos insuficientes');
});

test('C2PProcessResult is readonly', function () {
    $result = new C2PProcessResult(success: true);

    $reflection = new ReflectionClass($result);

    expect($reflection->isReadOnly())->toBeTrue();
});
