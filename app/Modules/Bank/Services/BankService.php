<?php

namespace App\Modules\Bank\Services;


use App\Modules\Bank\Repositories\BankRepositoryInterface;
use App\Modules\Sms\Enums\Action;
use App\Modules\Sms\Enums\Provider;
use App\Modules\Sms\SmsFactory;
use Illuminate\Http\JsonResponse;


class BankService implements BankServiceInterface
{
    public function __construct(public BankRepositoryInterface $repository)
    {
    }

    public function transfer(array $data): JsonResponse
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

        (new SmsFactory(Provider::KAVENEGAR))->send($result['destination'], Action::DEPOSIT, $data);
        (new SmsFactory(Provider::KAVENEGAR))->send($result['source'], Action::WITHDRAW, $data);

        return response()->json(['message' => 'Transfer money was successful'], 201);
    }
}
