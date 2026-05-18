<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Gateway;

use Danube\VePaySdk\Contracts\PaymentValidator;

readonly class VePaySdk
{
    public Conciliation $conciliation;

    public function __construct(PaymentValidator $driver)
    {
        $this->conciliation = new Conciliation($driver);
    }
}
