<?php

namespace App\Services\Api;

use App\Services\RatesApiInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ExchangeRatesApi implements RatesApiInterface
{
    private $config;
    private Client $client;

    public function __construct(Client $client)
    {
        $this->config = config('api.exchange_rates');
        $this->client = $client;
    }

    public function getRate(string $date): array
    {
        $url = $this->getUrl($date);

        try {
            $response = $this->client->request('GET', $url, ['verify' => false]);

            $rates = json_decode($response->getBody());

            return [
                'base' => $rates->base,
                'date' => $rates->date,
                'rates' => $rates->rates
            ];
        } catch (RequestException $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . '::' . $e->getMessage());

            return [];
        }
    }

    public function getUrl(string $date): string
    {
        $query = http_build_query([
            'access_key' => $this->config['key'],
            'symbols' => implode(',', $this->config['symbols']),
        ]);

        return $this->config['url'] . $date . '?' . $query;
    }
}
