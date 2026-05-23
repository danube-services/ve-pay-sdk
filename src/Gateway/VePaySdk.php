<?php

declare(strict_types=1);

namespace Danube\VePaySdk\Gateway;

use Danube\VePaySdk\Contracts\C2PPaymentDriver;
use Danube\VePaySdk\Contracts\PaymentValidator;

readonly class VePaySdk
{
    public Conciliation $conciliation;
    public ?C2PPayment $c2p;

    public function __construct(
        PaymentValidator $conciliationDriver,
        ?C2PPaymentDriver $c2pDriver = null,
    ) {
        $this->conciliation = new Conciliation($conciliationDriver);
        $this->c2p = $c2pDriver !== null ? new C2PPayment($c2pDriver) : null;
    }
}
