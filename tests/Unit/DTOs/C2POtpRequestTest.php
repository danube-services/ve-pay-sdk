<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2POtpRequest;

test('C2POtpRequest sets required fields', function () {
    $request = new C2POtpRequest(
        customerDocumentId: 'V12345678',
    );

    expect($request->customerDocumentId)->toBe('V12345678');
});

test('C2POtpRequest optional fields default to null', function () {
    $request = new C2POtpRequest(
        customerDocumentId: 'V12345678',
    );

    expect($request->customerPhone)->toBeNull();
});

test('C2POtpRequest metadata defaults to empty array', function () {
    $request = new C2POtpRequest(
        customerDocumentId: 'V12345678',
    );

    expect($request->metadata)->toBe([]);
});

test('C2POtpRequest accepts all fields', function () {
    $request = new C2POtpRequest(
        customerDocumentId: 'V87654321',
        customerPhone: '584241234567',
        metadata: ['extra' => 'value'],
    );

    expect($request->customerDocumentId)->toBe('V87654321')
        ->and($request->customerPhone)->toBe('584241234567')
        ->and($request->metadata)->toBe(['extra' => 'value']);
});

test('C2POtpRequest is readonly', function () {
    $request = new C2POtpRequest(
        customerDocumentId: 'V12345678',
    );

    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
