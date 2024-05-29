<?php

namespace App\Modules\Sms\Contracts;

use App\Models\CreditCard;
use App\Modules\Sms\Enums\Action;

interface SmsInterface
{
    public function send(CreditCard $creditCard, Action $action, array $data);
}
