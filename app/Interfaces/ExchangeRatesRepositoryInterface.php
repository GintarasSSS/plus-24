<?php

namespace App\Interfaces;

interface ExchangeRatesRepositoryInterface
{
    public function getRates(string $date);
}
