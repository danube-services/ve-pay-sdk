<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2PProcessRequest;

test('C2PProcessRequest sets required fields', function () {
    $request = new C2PProcessRequest(
        customerDocumentId: 'V12345678',
        customerPhone: '584241234567',
        amount: '1000.60',
        customerBankCode: '0102',
        otp: '5551111',
    );

    expect($request->customerDocumentId)->toBe('V12345678')
        ->and($request->customerPhone)->toBe('584241234567')
        ->and($request->amount)->toBe('1000.60')
        ->and($request->customerBankCode)->toBe('0102')
        ->and($request->otp)->toBe('5551111');
});

test('C2PProcessRequest optional fields default to null', function () {
    $request = new C2PProcessRequest(
        customerDocumentId: 'V12345678',
        customerPhone: '584241234567',
        amount: '1000.60',
        customerBankCode: '0102',
        otp: '5551111',
    );

    expect($request->concept)->toBeNull()
        ->and($request->coinType)->toBeNull()
        ->and($request->commercePhone)->toBeNull();
});

test('C2PProcessRequest metadata defaults to empty array', function () {
    $request = new C2PProcessRequest(
        customerDocumentId: 'V12345678',
        customerPhone: '584241234567',
        amount: '1000.60',
        customerBankCode: '0102',
        otp: '5551111',
    );

    expect($request->metadata)->toBe([]);
});

test('C2PProcessRequest accepts all fields', function () {
    $request = new C2PProcessRequest(
        customerDocumentId: 'V87654321',
        customerPhone: '584141234567',
        amount: '250.50',
        customerBankCode: '191',
        otp: '12345678',
        concept: 'Pago servicio',
        coinType: 'VES',
        commercePhone: '584241234567',
        metadata: ['terminal' => '15015840'],
    );

    expect($request->customerDocumentId)->toBe('V87654321')
        ->and($request->customerPhone)->toBe('584141234567')
        ->and($request->amount)->toBe('250.50')
        ->and($request->customerBankCode)->toBe('191')
        ->and($request->otp)->toBe('12345678')
        ->and($request->concept)->toBe('Pago servicio')
        ->and($request->coinType)->toBe('VES')
        ->and($request->commercePhone)->toBe('584241234567')
        ->and($request->metadata)->toBe(['terminal' => '15015840']);
});

test('C2PProcessRequest is readonly', function () {
    $request = new C2PProcessRequest(
        customerDocumentId: 'V12345678',
        customerPhone: '584241234567',
        amount: '1000.60',
        customerBankCode: '0102',
        otp: '5551111',
    );

    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
