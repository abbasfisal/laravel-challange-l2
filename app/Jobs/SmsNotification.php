<?php

namespace App\Jobs;

use App\Models\CreditCard;
use App\Modules\Sms\Enums\Action;
use App\Modules\Sms\SmsFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly CreditCard $creditCard, private readonly Action $action, private readonly array $data)
    {
    }


    public function handle(): void
    {
        SmsFactory::send($this->creditCard, $this->action, $this->data);
    }
}
