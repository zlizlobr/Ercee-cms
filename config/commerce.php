<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */
    'currency' => [
        'code' => env('CURRENCY_CODE', 'CZK'),
        'decimals' => (int) env('CURRENCY_DECIMALS', 2),
    ],
];
