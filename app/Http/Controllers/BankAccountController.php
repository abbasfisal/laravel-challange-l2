<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Modules\Bank\Services\BankServiceInterface;


class BankAccountController extends Controller
{

    public function __construct(public BankServiceInterface $service)
    {
    }

    public function transfer(TransferRequest $request)
    {
        return $this->service->transfer($request->validated());
    }

//        dd('s');

    }
}
