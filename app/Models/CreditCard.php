<?php

namespace App\Models;

use Database\Factories\CreditCardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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

    protected static function newFactory(): CreditCardFactory
    {
        return CreditCardFactory::new();
    }
}
