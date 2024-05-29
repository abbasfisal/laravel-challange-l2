<?php

namespace App\Models;

use Database\Factories\CreditCardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class CreditCard extends Model
{

    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'number'
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function sourceLogs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'source_card_id');
    }

    public function destinationLogs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'destination_card_id');
    }

    protected static function newFactory(): CreditCardFactory
    {
        return CreditCardFactory::new();
    }
}
