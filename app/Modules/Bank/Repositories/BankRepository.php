<?php

namespace App\Modules\Bank\Repositories;

use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BankRepository implements BankRepositoryInterface
{
    public function getAccountBy($creditCardNumber)
    {
        /** @var CreditCard $card */
        return CreditCard::query()
            ->where('number', $creditCardNumber)
            ->first();
    }

    /**
     * @throws ValidationException
     */
    public function updateBalance(array $data): array
    {

        DB::beginTransaction();
        try {
            /** @var CreditCard $source */
            $source = $this->getAccountBy($data['source_card_number']);

            /** @var CreditCard $destination */
            $destination = $this->getAccountBy($data['destination_card_number']);

            $sourceNewBalance = $source->bankAccount->balance - ($data['amount'] + config('bank.transaction.fee'));
            $source->bankAccount()->lockForUpdate()->update(['balance' => $sourceNewBalance]);

            $sourceTransaction = Transaction::query()->create([
                'credit_card_id' => $source->id,
                'type'           => Transaction::WITHDRAW,
                'amount'         => -$data['amount']
            ]);
            TransactionLog::query()->create([
                'transaction_id'      => $sourceTransaction->id,
                'source_card_id'      => $source->id,
                'destination_card_id' => $destination->id
            ]);
            //fee
            $sourceTransactionFee = Transaction::query()->create([
                'credit_card_id' => $source->id,
                'type'           => Transaction::FEE,
                'amount'         => -config('bank.transaction.fee')
            ]);


            $destinationNewBalance = $destination->bankAccount->balance + ($data['amount']);
            $destination->bankAccount()->lockForUpdate()->update(['balance' => $destinationNewBalance]);
            $destinationTransaction = Transaction::query()->create([
                'credit_card_id' => $destination->id,
                'type'           => Transaction::DEPOSIT,
                'amount'         => $data['amount']
            ]);
            TransactionLog::query()->create([
                'transaction_id'      => $destinationTransaction->id,
                'source_card_id'      => $source->id,
                'destination_card_id' => $destination->id
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

            throw ValidationException::withMessages(['transfer' => 'Transaction failed']);
        }
    }

    public function getLatestTenMinuteTransactions()
    {
        $tenMinutesAgo = Carbon::now()->subMinutes(10);


        $users = User::withCount(['transactions' => function ($query) use ($tenMinutesAgo) {
            $query
                ->where('type', '!=', Transaction::FEE)
                ->where('transactions.created_at', '>=', $tenMinutesAgo);
        }])
            ->orderBy('transactions_count', 'desc')
            ->take(3)
            ->get();


        $result = $users->map(function ($user) {
            $transactions = $user->transactions()
                ->where('type', '!=', Transaction::FEE)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return [
                'user'         => $user,
                'transactions' => $transactions
            ];
        });

        return $result;
    }

}

