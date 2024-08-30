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

        Mail::to('gintaras_sova@hotmail.com')->send(new ExchangeRatesMail($rates));
    }
}
