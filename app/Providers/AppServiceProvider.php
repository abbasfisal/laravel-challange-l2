<?php

namespace App\Providers;


use App\Modules\Bank\Repositories\BankRepository;
use App\Modules\Bank\Repositories\BankRepositoryInterface;
use App\Modules\Bank\Services\BankService;
use App\Modules\Bank\Services\BankServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(BankServiceInterface::class, BankService::class);
        $this->app->bind(BankRepositoryInterface::class, BankRepository::class);
    }

    /**
     * Bootstrap any application Modules.
     */
    public function boot(): void
    {
        //
    }
}
