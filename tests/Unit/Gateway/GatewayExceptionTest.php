<?php

declare(strict_types=1);

use Danube\VePaySdk\Gateway\GatewayException;

test('GatewayException is a RuntimeException', function () {
    $exception = new GatewayException('Timeout');

    expect($exception)->toBeInstanceOf(RuntimeException::class)
        ->and($exception->getMessage())->toBe('Timeout');
});
