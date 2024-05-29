<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_card_id',
        'destination_card_id',
        'amount'
    ];


    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function transactionFees(): HasMany
    {
        return $this->hasMany(TransactionFee::class);
    }


    public function sourceCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class, 'source_card_id');
    }

    public function destinationCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class, 'destination_card_id');
    }

}
