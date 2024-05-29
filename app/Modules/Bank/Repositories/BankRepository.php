<?php

namespace App\Modules\Bank\Repositories;

use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BankRepository implements BankRepositoryInterface
{
    public function GetAccountBy($creditCardNumber)
    {
        /** @var CreditCard $card */
        return CreditCard::query()
            ->with('bankAccount.user')
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
            //decrease source
            /** @var CreditCard $source */
            $source = $this->GetAccountBy($data['source_card_number']);

            /** @var CreditCard $destination */
            $destination = $this->GetAccountBy($data['destination_card_number']);


            $sourceNewBalance = $source->bankAccount->balance - ($data['amount'] + config('bank.transaction.fee'));
            $source->bankAccount()->lockForUpdate()->update(['balance' => $sourceNewBalance]);

            $sourceTransaction = Transaction::query()->create([
                'type'   => Transaction::WITHDRAW,
                'amount' => -$data['amount']
            ]);
            TransactionLog::query()->create([
                'transaction_id'      => $sourceTransaction->id,
                'source_card_id'      => $source->id,
                'destination_card_id' => $destination->id
            ]);
            //fee
            $sourceTransactionFee = Transaction::query()->create([
                'type'   => Transaction::FEE,
                'amount' => -config('bank.transaction.fee')
            ]);


            $destinationNewBalance = $destination->bankAccount->balance + ($data['amount']);
            $destination->bankAccount()->lockForUpdate()->update(['balance' => $destinationNewBalance]);
            $destinationTransaction = Transaction::query()->create([
                'type'   => Transaction::DEPOSIT,
                'amount' => $data['amount']
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

    public function getLatestTenMinuteTransactions(): Collection|array
    {

        //get transactions by time
        /** @var Transaction $recentTransactions */
        $recentTransactions = Transaction::query()->where('created_at', '>=', Carbon::now()->subMinutes(10))->get();


        //group transaction by user
        $userTransactionCounts = $recentTransactions->groupBy(function ($transaction) {
            return $transaction->sourceCard->bankAccount->user_id;
        })->map(function ($transactions, $userId) {
            return [
                'user_id' => $userId,
                'count'   => $transactions->count()
            ];
        })->sortByDesc('count')->take(3);


        $result = [];

        foreach ($userTransactionCounts as $user) {
            $userId = $user['user_id'];
            $userModel = User::query()->find($userId);
            $lastTenTransactions = Transaction::query()->whereHas('sourceCard.bankAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->orWhereHas('destinationCard.bankAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->orderBy('created_at', 'desc')->take(10)->get();

            $result[] = [
                'user'         => $userModel,
                'transactions' => $lastTenTransactions
            ];
        }

        return $result;
    }
}

