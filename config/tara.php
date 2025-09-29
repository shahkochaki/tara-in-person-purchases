<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tara Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for Tara in-person purchase service
    |
    */

    'base_url' => env('TARA_BASE_URL', 'https://stage.tara-club.ir/club/api/v1'),

    'username' => env('TARA_USERNAME', ''),

    'password' => env('TARA_PASSWORD', ''),

    'branch_code' => env('TARA_BRANCH_CODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable logging for Tara API calls
    |
    */
    'logging' => env('TARA_LOGGING', true),
];
