<?php

namespace App\Modules\Bank\Services;


use App\Modules\Bank\Repositories\BankRepositoryInterface;


class BankService implements BankServiceInterface
{
    public function __construct(public BankRepositoryInterface $repository)
    {
    }

    public function transfer(array $data)
    {

        $source = $this->repository->GetAccountBy($data['source_card_number']);
        $balance = $source->bankAccount->balance;

        if ($balance < ($data['amount'] + config('bank.transaction.fee'))) {
            return response()->json(['message' => 'balance is not enough'], 400);
        }


        $result = $this->repository->updateBalance($data);
        if (!$result) {
            return response()->json(['message' => 'Transaction failed'], 500);
        }

        return response()->json(['message' => 'Transfer Money successfully'], 201);

//        dd((new SmsFactory(Provider::KAVENEGAR))->send($result['destination'], Action::DEPOSIT, $data));
//        dd((new SmsFactory(Provider::KAVENEGAR))->send($result['source'], Action::WITHDRAW, $data));
//
//
//        (new SmsFactory(Provider::KAVENEGAR))->send($source, Action::WITHDRAW, $data);
//        (new SmsFactory(Provider::KAVENEGAR))->send($source, Action::DEPOSIT, $data);


    }
}
