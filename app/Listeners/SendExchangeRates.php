<?php

namespace App\Listeners;

use App\Events\ExchangeRatesGenerated;
use App\Mail\ExchangeRatesMail;
use Illuminate\Support\Facades\Mail;

class SendExchangeRates
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExchangeRatesGenerated $event): void
    {
        $rates = $event->rates;

        // API_EMAIL - shouldn't be hardcoded and should be taken from DB
        Mail::to(env('API_EMAIL'))->send(new ExchangeRatesMail($rates));
    }
}
