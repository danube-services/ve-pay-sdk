<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2POtpResult;

test('C2POtpResult sets all properties correctly', function () {
    $result = new C2POtpResult(
        success: true,
        message: 'OTP enviado',
        rawResponse: ['code' => '1000'],
    );

    expect($result->success)->toBeTrue()
        ->and($result->message)->toBe('OTP enviado')
        ->and($result->rawResponse)->toBe(['code' => '1000']);
});

test('C2POtpResult defaults message to empty string', function () {
    $result = new C2POtpResult(success: true);

    expect($result->message)->toBe('');
});

test('C2POtpResult defaults rawResponse to empty array', function () {
    $result = new C2POtpResult(success: false);

    expect($result->rawResponse)->toBe([]);
});

test('C2POtpResult failure state', function () {
    $result = new C2POtpResult(
        success: false,
        message: 'Cliente no encontrado',
    );

    expect($result->success)->toBeFalse()
        ->and($result->message)->toBe('Cliente no encontrado');
});

test('C2POtpResult is readonly', function () {
    $result = new C2POtpResult(success: true);

    $reflection = new ReflectionClass($result);

    expect($reflection->isReadOnly())->toBeTrue();
});
