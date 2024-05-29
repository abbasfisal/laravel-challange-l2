<?php

namespace App\Modules\Bank\Repositories;

use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\TransactionFee;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankRepository implements BankRepositoryInterface
{
    public function __construct()
    {
    }

    public function a()
    {
        dd('inside repos');
    }

    public function GetAccountBy($creditCardNumber)
    {
        /** @var CreditCard $card */
        return CreditCard::query()
            ->with('bankAccount.user')
            ->where('number', $creditCardNumber)
            ->first();
    }

    public function updateBalance(array $data)
    {

        DB::beginTransaction();
        try {
            //decrease source
            /** @var CreditCard $source */
            $source = $this->GetAccountBy($data['source_card_number']);

            /** @var CreditCard $destination */
            $destination = $this->GetAccountBy($data['destination_card_number']);


            $sourceNewBalance = $source->bankAccount->balance - ($data['amount'] + config('bank.transaction.fee'));
            $source->bankAccount()->lockForUpdate()->update(['balance' => $sourceNewBalance]);


            $destinationNewBalance = $destination->bankAccount->balance + ($data['amount']);
            $destination->bankAccount()->lockForUpdate()->update(['balance' => $destinationNewBalance]);

            //transaction table
            /** @var Transaction $transaction */
            $transaction = Transaction::query()
                ->sharedLock()
                ->create([
                    'source_card_id'      => $source->id,
                    'destination_card_id' => $destination->id,
                    'amount'              => $data['amount']
                ]);

            TransactionFee::query()
                ->create([
                    'transaction_id' => $transaction->id,
                    'fee_amount'     => config('bank.transaction.fee')
                ]);

            DB::commit();

            Log::info('Transaction was success', [
                'source'      => $source->refresh()->toArray(),
                'destination' => $destination->refresh()->toArray()
            ]);

            return [
                'source'      => $source->refresh(),
                'destination' => $destination->refresh()
            ];

        } catch (Exception $e) {
            Log::critical('Transaction Failed', ['message' => $e->getMessage(), 'code' => $e->getCode(), 'data' => $data]);
            DB::rollBack();

            return false;
        }
    }

}

//todo: bala ro anjam bede
