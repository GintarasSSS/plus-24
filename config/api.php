<?php

return [
    'exchange_rates' => [
        'url' => env('API_URL'),
        'key' => env('API_KEY'),
        'base' => 'USD',
        'symbols' => [
            "AED",
            "AFN",
            "ALL",
            "AMD",
            "AUD",
            "CAD",
            "CHF",
            "CNY",
            "GBP",
            "JPY"
        ]
    ]
];
