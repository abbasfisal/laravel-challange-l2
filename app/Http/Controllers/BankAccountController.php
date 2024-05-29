<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Http\Resources\TopUserTransactions;
use App\Modules\Bank\Services\BankServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class BankAccountController extends Controller
{

    public function __construct(public BankServiceInterface $service)
    {
    }

    public function transfer(TransferRequest $request)
    {
        return $this->service->transfer($request->validated());
    }

    public function topUsersWithTransactions(): AnonymousResourceCollection
    {
        $result = $this->service->getTopUserByTransactions();
        return TopUserTransactions::collection($result);
    }
}
