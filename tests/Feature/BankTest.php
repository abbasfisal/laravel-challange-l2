<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CreditCard;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BankTest extends TestCase
{
    use DatabaseTransactions;

    const URL = "/api/transfer";

    public function test_post_empty(): void
    {
        $this->postJson(self::URL)->assertStatus(422);
    }

    public function test_transfer_money()
    {

        //---------------------
        //      prepare Data
        //---------------------
        /** @var CreditCard $creditCard */
        $sourceCreditCard = CreditCard::factory()->create();
        $sourceBalance = $sourceCreditCard->bankAccount->balance;


        /** @var CreditCard $destinationCreditCard */
        $destinationCreditCard = CreditCard::factory()->create();
        $destinationBalance = $destinationCreditCard->bankAccount->balance;

        //---------------------
        //      perform Action
        //---------------------

        $transferMoney = rand(111, 999) * 1000;
        $response = $this->postJson(self::URL, [
            "source_card_number"      => $sourceCreditCard->number,
            "destination_card_number" => $destinationCreditCard->number,
            "amount"                  => $transferMoney
        ])
            ->assertStatus(201)
            ->json();


        //---------------------
        //      database Assertion
        //---------------------

        $this->assertDatabaseHas('credit_cards',
            ['bank_account_id' => $sourceCreditCard->bank_account_id, 'number' => $sourceCreditCard->number]
        );

        //*** check balance after withdraw + fee
        $this->assertDatabaseHas('bank_accounts',
            [
                'user_id' => $sourceCreditCard->bankAccount->user_id,
                'balance' => $sourceBalance - (config('bank.transaction.fee') + $transferMoney),
                'number'  => (string)$sourceCreditCard->bankAccount->number
            ]
        );

        //*** check balance deposit
        $this->assertDatabaseHas('bank_accounts',
            [
                'user_id' => $destinationCreditCard->bankAccount->user_id,
                'balance' => $destinationBalance + $transferMoney,
                'number'  => (string)$destinationCreditCard->bankAccount->number
            ]
        );
        /** @var Transaction $transaction */
        $transaction = Transaction::query()
            ->where([
                'source_card_id'      => $sourceCreditCard->id,
                'destination_card_id' => $destinationCreditCard->id,
                'amount'              => $transferMoney])
            ->first();

        //*** check transaction
        $this->assertDatabaseHas('transactions', [
            'source_card_id'      => $sourceCreditCard->id,
            'destination_card_id' => $destinationCreditCard->id,
            'amount'              => $transferMoney
        ]);

        //*** check transaction_fees
        $this->assertDatabaseHas('transaction_fees', [
            'transaction_id' => $transaction->id,
            'fee_amount'     => config('bank.transaction.fee')
        ]);

        //validation min , max =>check
        //validation credit card => check
        //validation for checking balance
    }


    public function test_balance_is_not_enough()
    {

        //---------------------
        //      prepare Data
        //---------------------
        /** @var CreditCard $creditCard */
        $sourceCreditCard = CreditCard::factory()->create();
        $sourceBalance = $sourceCreditCard->bankAccount->balance;

        $sourceCreditCard->bankAccount()->update(['balance' => 200]);

        /** @var CreditCard $destinationCreditCard */
        $destinationCreditCard = CreditCard::factory()->create();
        $destinationBalance = $destinationCreditCard->bankAccount->balance;

        //---------------------
        //      perform Action
        //---------------------

        $transferMoney = rand(111, 999) * 1000;
        $response = $this->postJson(self::URL,
            [
                "source_card_number"      => $sourceCreditCard->number,
                "destination_card_number" => $destinationCreditCard->number,
                "amount"                  => $transferMoney
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "message" => "balance is not enough"
            ])
            ->json();

    }
}
