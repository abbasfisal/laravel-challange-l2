<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name'   => 'Ali',
            'mobile' => '09120000000',
        ]);

        User::factory()->create([
            'name'   => 'Reza',
            'mobile' => '09130000000',
        ]);

        BankAccount::query()->create([
            'user_id' => 1, //ali
            'balance' => 500000,
            'number'  => '1111111111'
        ]);
        CreditCard::query()->create([
            'bank_account_id' => 1, //ali
            'number'          => '5892101575367038'
        ]);

        BankAccount::query()->create([
            'user_id' => 2, //reza
            'balance' => 400000,
            'number'  => '2111111111'
        ]);
        CreditCard::query()->create([
            'bank_account_id' => 2, //ali
            'number'          => generateBankCardNumber()
        ]);

    }


}

