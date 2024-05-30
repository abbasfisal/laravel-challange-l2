<?php

namespace App\Http\Requests;

use App\Rules\ValidIranCreditCardNumber;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source_card_number'      => ['required', 'integer', new ValidIranCreditCardNumber(), 'digits:16', 'exists:credit_cards,number'],
            'destination_card_number' => ['required', 'integer', new ValidIranCreditCardNumber(), 'digits:16', 'exists:credit_cards,number'],
            'amount'                  => ['required', 'integer', 'min:1000', 'max:50000000'],
        ];
    }
}
