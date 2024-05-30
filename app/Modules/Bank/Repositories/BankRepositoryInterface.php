<?php

namespace App\Modules\Bank\Repositories;

interface BankRepositoryInterface
{
    public function getAccountBy($creditCardNumber);

    public function updateBalance(array $data);

    public function getLatestTenMinuteTransactions();
}
