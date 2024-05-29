<?php

namespace App\Modules\Sms\Providers;

use App\Models\CreditCard;
use App\Modules\Sms\Contracts\SmsInterface;
use App\Modules\Sms\Enums\Action;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Ghasedak implements SmsInterface
{
    /**
     * @throws ConnectionException
     */
    public function send(CreditCard $creditCard, Action $action, array $data)
    {
        $prepare = [
            'receptor'   => $creditCard->bankAccount->user->mobile,
            'linenumber' => '30005088',//free
            'message'    => sprintf("%s" . " %s تومان" . " موجودی %s تومان", $action->value, $data['amount'], $creditCard->bankAccount->balance)
        ];

        Http::withHeaders(['apikey' => env('GHASEDAK_API_KEY')])
            ->post(env('GHASEDAK_URL'), $prepare);


    }
}
