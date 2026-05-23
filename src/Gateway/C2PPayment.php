<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Gateway;

use Danube\VePaySdk\Contracts\C2PPaymentDriver;
use Danube\VePaySdk\DTOs\C2PAnnulRequest;
use Danube\VePaySdk\DTOs\C2PAnnulResult;
use Danube\VePaySdk\DTOs\C2POtpRequest;
use Danube\VePaySdk\DTOs\C2POtpResult;
use Danube\VePaySdk\DTOs\C2PProcessRequest;
use Danube\VePaySdk\DTOs\C2PProcessResult;

readonly class C2PPayment
{
    public function __construct(
        private C2PPaymentDriver $driver,
    ) {
    }

    public function requestOtp(C2POtpRequest $request): C2POtpResult
    {
        return $this->driver->requestOtp($request);
    }

    public function pay(C2PProcessRequest $request): C2PProcessResult
    {
        return $this->driver->pay($request);
    }

    public function annul(C2PAnnulRequest $request): C2PAnnulResult
    {
        return $this->driver->annul($request);
    }
}
