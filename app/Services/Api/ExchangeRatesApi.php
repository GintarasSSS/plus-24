<?php

namespace App\Services\Api;

use App\Services\RatesApiInterface;
use GuzzleHttp\Client;
use Mockery\Exception;

class ExchangeRatesApi implements RatesApiInterface
{
    private $config;

    public function __construct()
    {
        $this->config = config('api.exchange_rates');
    }

    public function getRate(string $date)
    {
        $result = [];

        $query = http_build_query([
            'access_key' => $this->config['key'],
            'symbols' => implode(',', $this->config['symbols']),
        ]);

        $url = $this->config['url'] . $date . '?' . $query;

        try {
            $response = (new Client(['verify' => false]))->get($url);

            $rates = json_decode($response->getBody());

            return [
                'base' => $rates->base,
                'date' => $rates->date,
                'rates' => $rates->rates
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . '::' . $e->getMessage());

            return $result;
        }
    }
}
