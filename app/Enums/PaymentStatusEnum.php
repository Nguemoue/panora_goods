<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case NOT_INITIATED = 'not_initiated';
    case PAID = 'paid';
}
