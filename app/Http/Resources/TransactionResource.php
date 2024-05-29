<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'source_card_id'      => $this->source_card_id,
            'destination_card_id' => $this->destination_card_id,
            'amount'              => $this->amount,
            'created_at'          => $this->created_at
        ];
    }
}
