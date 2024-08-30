<?php

namespace App\Providers;

use App\Interfaces\ExchangeRatesRepositoryInterface;
use App\Repositories\ExchangeRatesRepository;
use Illuminate\Support\ServiceProvider;

class ExchangeRatesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ExchangeRatesRepositoryInterface::class, ExchangeRatesRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
