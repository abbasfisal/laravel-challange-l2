<?php

use App\Http\Controllers\BankAccountController;
use Illuminate\Support\Facades\Route;


Route::post("/transfer", [BankAccountController::class, 'transfer']);

Route::get('top-users-transactions', [BankAccountController::class, 'topUsersWithTransactions']);
