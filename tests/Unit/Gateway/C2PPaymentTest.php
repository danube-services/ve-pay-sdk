<?php

declare(strict_types=1);

use Danube\VePaySdk\Contracts\C2PPaymentDriver;
use Danube\VePaySdk\DTOs\C2PAnnulRequest;
use Danube\VePaySdk\DTOs\C2PAnnulResult;
use Danube\VePaySdk\DTOs\C2POtpRequest;
use Danube\VePaySdk\DTOs\C2POtpResult;
use Danube\VePaySdk\DTOs\C2PProcessRequest;
use Danube\VePaySdk\DTOs\C2PProcessResult;
use Danube\VePaySdk\Gateway\C2PPayment;

test('C2PPayment requestOtp delegates to driver', function () {
    $expectedResult = new C2POtpResult(success: true, message: 'OTP enviado');

    $driver = new readonly class ($expectedResult) implements C2PPaymentDriver {
        public function __construct(private C2POtpResult $result)
        {
        }
        public function requestOtp(C2POtpRequest $request): C2POtpResult
        {
            return $this->result;
        }
        public function pay(C2PProcessRequest $request): C2PProcessResult
        {
            return new C2PProcessResult(success: false);
        }
        public function annul(C2PAnnulRequest $request): C2PAnnulResult
        {
            return new C2PAnnulResult(success: false);
        }
    };

    $handler = new C2PPayment($driver);
    $result = $handler->requestOtp(new C2POtpRequest(customerDocumentId: 'V12345678'));

    expect($result->success)->toBeTrue()
        ->and($result->message)->toBe('OTP enviado');
});

test('C2PPayment pay delegates to driver', function () {
    $expectedResult = new C2PProcessResult(
        success: true,
        transactionId: '01020102984000790909404165892642',
        reference: '090037579602',
        date: '2025-11-14',
        message: 'Pago procesado',
    );

    $driver = new readonly class ($expectedResult) implements C2PPaymentDriver {
        public function __construct(private C2PProcessResult $result)
        {
        }
        public function requestOtp(C2POtpRequest $request): C2POtpResult
        {
            return new C2POtpResult(success: false);
        }
        public function pay(C2PProcessRequest $request): C2PProcessResult
        {
            return $this->result;
        }
        public function annul(C2PAnnulRequest $request): C2PAnnulResult
        {
            return new C2PAnnulResult(success: false);
        }
    };

    $handler = new C2PPayment($driver);
    $result = $handler->pay(new C2PProcessRequest(
        customerDocumentId: 'V12345678',
        customerPhone: '584241234567',
        amount: '1000.60',
        customerBankCode: '0102',
        otp: '5551111',
    ));

    expect($result->success)->toBeTrue()
        ->and($result->transactionId)->toBe('01020102984000790909404165892642')
        ->and($result->reference)->toBe('090037579602')
        ->and($result->date)->toBe('2025-11-14')
        ->and($result->message)->toBe('Pago procesado');
});

test('C2PPayment annul delegates to driver', function () {
    $expectedResult = new C2PAnnulResult(
        success: true,
        transactionId: '26137',
        reference: '000000',
        message: 'C2P reversado exitosamente',
    );

    $driver = new readonly class ($expectedResult) implements C2PPaymentDriver {
        public function __construct(private C2PAnnulResult $result)
        {
        }
        public function requestOtp(C2POtpRequest $request): C2POtpResult
        {
            return new C2POtpResult(success: false);
        }
        public function pay(C2PProcessRequest $request): C2PProcessResult
        {
            return new C2PProcessResult(success: false);
        }
        public function annul(C2PAnnulRequest $request): C2PAnnulResult
        {
            return $this->result;
        }
    };

    $handler = new C2PPayment($driver);
    $result = $handler->annul(new C2PAnnulRequest(
        transactionId: '26119',
        amount: '101.00',
        terminal: '15015840',
    ));

    expect($result->success)->toBeTrue()
        ->and($result->transactionId)->toBe('26137')
        ->and($result->reference)->toBe('000000')
        ->and($result->message)->toBe('C2P reversado exitosamente');
});
