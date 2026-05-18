<?php

declare(strict_types=1);

use Danube\VePaySdk\DTOs\PaymentStatus;

test('PaymentStatus has exactly four cases', function () {
    $cases = PaymentStatus::cases();

    expect($cases)->toHaveCount(4);
});

test('PaymentStatus cases have correct string values', function () {
    expect(PaymentStatus::Matched->value)->toBe('matched')
        ->and(PaymentStatus::NotMatched->value)->toBe('not_matched')
        ->and(PaymentStatus::AlreadyReconciled->value)->toBe('already_reconciled')
        ->and(PaymentStatus::Error->value)->toBe('error');
});

test('PaymentStatus from method works', function () {
    expect(PaymentStatus::from('matched'))->toBe(PaymentStatus::Matched)
        ->and(PaymentStatus::from('not_matched'))->toBe(PaymentStatus::NotMatched)
        ->and(PaymentStatus::from('already_reconciled'))->toBe(PaymentStatus::AlreadyReconciled)
        ->and(PaymentStatus::from('error'))->toBe(PaymentStatus::Error);
});

test('PaymentStatus tryFrom returns null for invalid values', function () {
    expect(PaymentStatus::tryFrom('nonexistent'))->toBeNull();
});
