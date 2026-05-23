<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\C2PAnnulRequest;

test('C2PAnnulRequest all fields default to null', function () {
    $request = new C2PAnnulRequest();

    expect($request->transactionId)->toBeNull()
        ->and($request->amount)->toBeNull()
        ->and($request->terminal)->toBeNull()
        ->and($request->debtorDoc)->toBeNull()
        ->and($request->debtorPhone)->toBeNull()
        ->and($request->bankCode)->toBeNull();
});

test('C2PAnnulRequest metadata defaults to empty array', function () {
    $request = new C2PAnnulRequest();

    expect($request->metadata)->toBe([]);
});

test('C2PAnnulRequest by transaction ID', function () {
    $request = new C2PAnnulRequest(
        transactionId: '01020102984000790909404165892642',
        amount: '100.50',
        terminal: '15015840',
    );

    expect($request->transactionId)->toBe('01020102984000790909404165892642')
        ->and($request->amount)->toBe('100.50')
        ->and($request->terminal)->toBe('15015840')
        ->and($request->debtorDoc)->toBeNull()
        ->and($request->debtorPhone)->toBeNull()
        ->and($request->bankCode)->toBeNull();
});

test('C2PAnnulRequest by debtor info', function () {
    $request = new C2PAnnulRequest(
        debtorDoc: 'V23000760',
        debtorPhone: '584242207524',
        bankCode: '191',
        amount: '101.00',
        terminal: '15015840',
    );

    expect($request->debtorDoc)->toBe('V23000760')
        ->and($request->debtorPhone)->toBe('584242207524')
        ->and($request->bankCode)->toBe('191')
        ->and($request->amount)->toBe('101.00')
        ->and($request->terminal)->toBe('15015840')
        ->and($request->transactionId)->toBeNull();
});

test('C2PAnnulRequest accepts metadata', function () {
    $request = new C2PAnnulRequest(
        transactionId: '36504',
        metadata: ['childClientId' => 'V012345678', 'branchId' => 'CCS001'],
    );

    expect($request->metadata)->toBe(['childClientId' => 'V012345678', 'branchId' => 'CCS001']);
});

test('C2PAnnulRequest is readonly', function () {
    $request = new C2PAnnulRequest();

    $reflection = new ReflectionClass($request);

    expect($reflection->isReadOnly())->toBeTrue();
});
