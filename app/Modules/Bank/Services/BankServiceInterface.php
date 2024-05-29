<?php

namespace App\Modules\Bank\Services;

interface BankServiceInterface
{
    public function transfer(array $data);

    public function getTopUserByTransactions();
}
