<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id'     => $this['id'],
            'type'   => $this->getTypeString(),
            'amount' => $this['amount'],
        ];
    }

    /**
     * @return string
     */
    public function getTypeString(): string
    {
        $type = '';
        switch ($this['type']) {
            case Transaction::WITHDRAW:
                $type = 'WITHDRAW';
                break;

            case Transaction::DEPOSIT:
                $type = 'DEPOSIT';
                break;

            case Transaction::FEE:
                $type = 'FEE';
                break;
        }
        return $type;
    }
}
