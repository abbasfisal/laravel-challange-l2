<?php

namespace App\Modules\Bank\Services;


use App\Jobs\SmsNotification;
use App\Modules\Bank\Repositories\BankRepositoryInterface;
use App\Modules\Sms\Enums\Action;
use Illuminate\Validation\ValidationException;


class BankService implements BankServiceInterface
{
    public function __construct(public BankRepositoryInterface $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function transfer(array $data)
    {
        $source = $this->repository->getAccountBy($data['source_card_number']);
        $balance = $source->bankAccount->balance;

        if ($balance < ($data['amount'] + config('bank.transaction.fee'))) {
            throw ValidationException::withMessages(['amount' => 'balance is not enough']);
        }

        $result = $this->repository->updateBalance($data);


        SmsNotification::dispatch($result['source'], Action::WITHDRAW, $data);
        SmsNotification::dispatch($result['destination'], Action::DEPOSIT, $data);
    }

    public function getTopUserByTransactions()
    {
        return $this->repository->getLatestTenMinuteTransactions();
    }
}
