<?php

namespace App\Modules\Sms;

use App\Models\CreditCard;
use App\Modules\Sms\Enums\Action;
use App\Modules\Sms\Providers\Ghasedak;
use App\Modules\Sms\Providers\Kavenegar;

class SmsFactory
{

    public static function send(CreditCard $creditCard, Action $action, array $data): void
    {
        switch (env('SMS_PROVIDER')) {
            case 'kavenegar':
                (new Kavenegar())->send($creditCard, $action, $data);
                break;
            case 'ghasedak':
                (new Ghasedak())->send($creditCard, $action, $data);
                break;
        }
    }
}
