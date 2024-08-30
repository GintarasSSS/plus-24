<?php

namespace App\Jobs;

use App\Models\Rate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $rate;

    public function __construct(array $rate)
    {
        $this->rate = $rate;
    }

    public function handle(): void
    {
        $rate = new Rate();

        $rate->base = $this->rate['base'];
        $rate->date = $this->rate['date'];
        $rate->rates = $this->rate['rates'];

        $rate->save();
    }
}
