<?php

use App\Support\DevLayer\ErceeDevLayerPolicy;

return ErceeDevLayerPolicy::resolve([
    'ERCEE_DEV_LAYER' => env('ERCEE_DEV_LAYER'),
    'ERCEE_LOG_LEVEL' => env('ERCEE_LOG_LEVEL'),
    'ERCEE_PUBLIC_DEBUG' => env('ERCEE_PUBLIC_DEBUG'),
    'ERCEE_RUNTIME_PROFILE' => env('ERCEE_RUNTIME_PROFILE'),
    'APP_ENV' => env('APP_ENV'),
    'APP_DEBUG' => env('APP_DEBUG'),
    'LOG_LEVEL' => env('LOG_LEVEL'),
]);
