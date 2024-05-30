<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Middleware\NoneEngToEngDigit;
use Illuminate\Support\Facades\Route;


Route::post("/transfer", [BankAccountController::class, 'transfer'])->middleware(NoneEngToEngDigit::class);

Route::get('top-users-transactions', [BankAccountController::class, 'topUsersWithTransactions']);
