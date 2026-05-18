<?php

declare(strict_types=1);

namespace Danube\VePaySdk\DTOs;

enum PaymentStatus: string
{
    case Matched = 'matched';
    case NotMatched = 'not_matched';
    case AlreadyReconciled = 'already_reconciled';
    case Error = 'error';
}
