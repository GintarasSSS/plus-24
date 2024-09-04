<?php

namespace App\Repositories;

use App\Interfaces\ExchangeRatesRepositoryInterface;
use App\Jobs\SaveExchangeRates;
use App\Models\Rate;
use App\Services\Api\ExchangeRatesApi;
use Illuminate\Support\Facades\Cache;

class ExchangeRatesRepository implements ExchangeRatesRepositoryInterface
{
    const CACHE_KEY = 'exchange_rates::';

    private ExchangeRatesApi $api;
    private Rate $rate;

    public function __construct(Rate $rate, ExchangeRatesApi $api)
    {
        $this->api = $api;
        $this->rate = $rate;
    }

    public function getRates(string $date): array
    {
        if (!($result = Cache::get(self::CACHE_KEY. $date))) {
            $result = $this->rate::query()->where('date', $date)->first(['base', 'date', 'rates']);

            Cache::put(self::CACHE_KEY. $date, $result);
        }

        if (!$result) {
            $apiRates = $this->api->getRate($date);

            if ($apiRates) {
                dispatch(new SaveExchangeRates($apiRates));

                return $apiRates;
            }
        }

        return $result ? $result->toArray() : [];
    }
}
