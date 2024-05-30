<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditCard>
 */
class CreditCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //$valid = ['6037697514868204', '6395991179379862', '6362141808807854'];
        return [
            'bank_account_id' => BankAccount::factory(),
            'user_id'         => function (array $attributes) {
                return BankAccount::query()->find($attributes['bank_account_id'])->user_id;
            },
            'number'          => generateBankCardNumber()
        ];
    }
}
