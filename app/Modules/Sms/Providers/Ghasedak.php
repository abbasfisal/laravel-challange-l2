<?php

namespace App\Modules\Sms\Providers;

use App\Models\CreditCard;
use App\Modules\Sms\Contracts\SmsInterface;
use App\Modules\Sms\Enums\Action;
use Illuminate\Support\Facades\Log;

class Ghasedak implements SmsInterface
{

    public function send(CreditCard $creditCard, Action $action, array $data)
    {
        $prepare = [
            'receptor'   => $creditCard->bankAccount->user->mobile,
            'linenumber' => '30005088',//free

        ];

        if ($action == Action::DEPOSIT) {
            $prepare['message'] = sprintf("Deposit :%s , current balance is: %s", $data['amount'], $creditCard->bankAccount->balance);
        } else {
            $prepare['message'] = sprintf("Withdraw+transaction fee: %s + %s , current balance is: %s", $data['amount'], config('bank.transaction.fee'), $creditCard->bankAccount->balance);
        }

        Log::info('Gasedak sent sms', [$prepare]);

//        Http::withHeaders(['apikey' => env('GHASEDAK_API_KEY')])
//            ->post(env('GHASEDAK_URL'), $prepare);

    }
}
