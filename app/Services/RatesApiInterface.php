<?php

namespace App\Services;

interface RatesApiInterface
{
    public function getRate(string $date): array;
    public function getUrl(string $date): string;
}
