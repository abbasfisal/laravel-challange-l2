<?php

namespace App\Modules\Sms\Providers;

use App\Models\CreditCard;
use App\Modules\Sms\Contracts\SmsInterface;
use App\Modules\Sms\Enums\Action;
use Illuminate\Support\Facades\Http;

class Kavenegar implements SmsInterface
{
    public function send(CreditCard $creditCard, Action $action, array $data)
    {

        $prepare = [
            'receptor' => $creditCard->bankAccount->user->mobile,
        ];

        if ($action == Action::DEPOSIT) {
            $prepare['message'] = sprintf("Deposit :%s , current balance is: %s", $data['amount'], $creditCard->bankAccount->balance);
        } else {
            $prepare['message'] = sprintf("Withdraw+transaction fee: %s + %s , current balance is: %s", $data['amount'], config('bank.transaction.fee'), $creditCard->bankAccount->balance);
        }

dd($prepare);
        //todo: add retry or try catch
        Http::retry(3)->get(env('KAVENEGAR_URL'), $prepare);

    }
}
