<?php

namespace App\Modules\Bank\Repositories;

interface BankRepositoryInterface
{
    public function a();

    public function GetAccountBy($creditCardNumber);

    public function updateBalance(array $data);

    public function getLatestTenMinuteTransactions();
}
