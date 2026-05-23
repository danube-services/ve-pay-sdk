<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Contracts;

use Danube\VePaySdk\DTOs\C2PAnnulRequest;
use Danube\VePaySdk\DTOs\C2PAnnulResult;
use Danube\VePaySdk\DTOs\C2POtpRequest;
use Danube\VePaySdk\DTOs\C2POtpResult;
use Danube\VePaySdk\DTOs\C2PProcessRequest;
use Danube\VePaySdk\DTOs\C2PProcessResult;

interface C2PPaymentDriver
{
    public function requestOtp(C2POtpRequest $request): C2POtpResult;

    public function pay(C2PProcessRequest $request): C2PProcessResult;

    public function annul(C2PAnnulRequest $request): C2PAnnulResult;
}
