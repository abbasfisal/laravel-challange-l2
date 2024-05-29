<?php

namespace App\Http\Requests;

use App\Rules\ValidIranCreditCardNumber;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        //change to middleware
        $this->merge([
            'source_card_number'      => (int)convertPersianToEnglish($this->request->get('source_card_number')),
            'destination_card_number' => (int)convertPersianToEnglish($this->request->get('destination_card_number')),
            'amount'                  => (int)convertPersianToEnglish($this->request->get('amount')),
        ]);


    }

    public function rules(): array
    {
        return [
            'source_card_number'      => ['required', 'integer', new ValidIranCreditCardNumber(), 'digits:16', 'exists:credit_cards,number'],
            'destination_card_number' => ['required', 'integer', 'digits:16', 'exists:credit_cards,number'],
            'amount'                  => ['required', 'integer', 'min:1000', 'max:50000000'],
        ];
    }
}
