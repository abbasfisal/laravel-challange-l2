<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    const DEPOSIT = 1;
    const WITHDRAW = 2;
    const FEE = 3;

    use HasFactory;

    protected $fillable = [
        'credit_card_id',
        'type',
        'amount'
    ];

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class);
    }
}
