<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2PAnnulResult;

test('C2PAnnulResult sets all properties correctly', function () {
    $result = new C2PAnnulResult(
        success: true,
        transactionId: '26137',
        reference: '000000',
        message: 'C2P reversado exitosamente',
        rawResponse: ['IdTransaction' => 26137, 'Reference' => '000000'],
    );

    expect($result->success)->toBeTrue()
        ->and($result->transactionId)->toBe('26137')
        ->and($result->reference)->toBe('000000')
        ->and($result->message)->toBe('C2P reversado exitosamente')
        ->and($result->rawResponse)->toBe(['IdTransaction' => 26137, 'Reference' => '000000']);
});

test('C2PAnnulResult nullable fields default to null', function () {
    $result = new C2PAnnulResult(success: true);

    expect($result->transactionId)->toBeNull()
        ->and($result->reference)->toBeNull();
});

test('C2PAnnulResult message defaults to empty string', function () {
    $result = new C2PAnnulResult(success: false);

    expect($result->message)->toBe('');
});

test('C2PAnnulResult rawResponse defaults to empty array', function () {
    $result = new C2PAnnulResult(success: false);

    expect($result->rawResponse)->toBe([]);
});

test('C2PAnnulResult failure state', function () {
    $result = new C2PAnnulResult(
        success: false,
        message: 'Transacción no encontrada',
    );

    expect($result->success)->toBeFalse()
        ->and($result->message)->toBe('Transacción no encontrada');
});

test('C2PAnnulResult is readonly', function () {
    $result = new C2PAnnulResult(success: true);

    $reflection = new ReflectionClass($result);

    expect($reflection->isReadOnly())->toBeTrue();
});
