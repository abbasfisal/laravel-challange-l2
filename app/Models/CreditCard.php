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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function newFactory(): CreditCardFactory
    {
        return CreditCardFactory::new();
    }
}
