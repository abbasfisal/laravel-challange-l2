<?php

namespace App\Modules\Sms\Enums;

enum Action: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
