<?php

namespace App\Modules\Sms;

use App\Models\CreditCard;
use App\Modules\Sms\Enums\Action;
use App\Modules\Sms\Enums\Provider;
use App\Modules\Sms\Providers\Ghasedak;
use App\Modules\Sms\Providers\Kavenegar;
use Illuminate\Http\Client\ConnectionException;

class SmsFactory
{

    public function __construct(private readonly Provider $provider )
    {
    }

    /**
     * @throws ConnectionException
     */
    public function Send(CreditCard $creditCard, Action $action, array $data): void
    {
        switch ($this->provider->value) {
            case Provider::KAVENEGAR->value:
                (new Kavenegar())->send($creditCard, $action, $data);
                break;
            case Provider::GASEDAK->value:
                (new Ghasedak())->send($creditCard, $action, $data);
                break;
        }
    }
}
