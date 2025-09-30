<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tara API Configuration
    |--------------------------------------------------------------------------
    |
    | تنظیمات API سرویس تارا برای پرداخت‌های حضوری
    | This file contains configuration for Tara in-person purchase service
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | آدرس پایه API تارا
    | برای محیط تست: https://stage.tara-club.ir/club/api/v1
    | برای محیط اصلی: https://api.tara-club.ir/club/api/v1
    |
    */
    'base_url' => env('TARA_BASE_URL', 'https://stage.tara-club.ir/club/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | اطلاعات احراز هویت برای دسترسی به API
    | Authentication credentials for API access
    |
    */
    'credentials' => [
        'username' => env('TARA_USERNAME'),
        'password' => env('TARA_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Branch Code
    |--------------------------------------------------------------------------
    |
    | کد شعبه پیش‌فرض
    | Default branch code
    |
    */
    'default_branch_code' => env('TARA_BRANCH_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    |
    | تنظیمات زمان انقضا برای درخواست‌های HTTP
    | HTTP request timeout settings
    |
    */
    'timeout' => [
        'connect' => env('TARA_CONNECT_TIMEOUT', 30), // ثانیه / seconds
        'request' => env('TARA_REQUEST_TIMEOUT', 60), // ثانیه / seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Settings
    |--------------------------------------------------------------------------
    |
    | تنظیمات مربوط به مدیریت توکن
    | Token management settings
    |
    */
    'token' => [
        'buffer_seconds' => env('TARA_TOKEN_BUFFER', 60), // بافر قبل از انقضا توکن
        'auto_refresh' => env('TARA_AUTO_REFRESH_TOKEN', true), // تمدید خودکار توکن
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | تنظیمات مربوط به لاگ
    | Logging configuration
    |
    */
    'logging' => [
        'enabled' => env('TARA_LOGGING_ENABLED', true),
        'level' => env('TARA_LOG_LEVEL', 'info'), // debug, info, warning, error
        'channel' => env('TARA_LOG_CHANNEL', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Settings
    |--------------------------------------------------------------------------
    |
    | تنظیمات محیط
    | Environment configuration
    |
    */
    'environment' => env('TARA_ENVIRONMENT', 'staging'), // staging, production

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | آدرس‌های مختلف API
    | API endpoint paths
    |
    */
    'endpoints' => [
        'login' => '/user/login/merchant',
        'access_code' => '/merchant/access/code',
        'merchandise_groups' => '/purchase/merchandise/groups',
        'purchase_trace' => '/purchase/trace',
        'purchase_request' => '/purchase/request',
        'purchase_verify' => '/purchase/verify',
        'purchase_reverse' => '/purchase/reverse',
        'purchase_inquiry' => '/purchase/inquiry',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Headers
    |--------------------------------------------------------------------------
    |
    | هدرهای پیش‌فرض برای درخواست‌ها
    | Default headers for HTTP requests
    |
    */
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Cookie' => env('TARA_COOKIE', 'SERVER_USED=srv2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | تنظیمات تکرار درخواست در صورت خطا
    | Request retry configuration
    |
    */
    'retry' => [
        'enabled' => env('TARA_RETRY_ENABLED', true),
        'max_attempts' => env('TARA_MAX_RETRY_ATTEMPTS', 3),
        'delay_ms' => env('TARA_RETRY_DELAY', 1000), // میلی‌ثانیه / milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Support
    |--------------------------------------------------------------------------
    |
    | پشتیبانی از کانفیگ قدیمی
    | Support for old configuration keys
    |
    */

    // For backward compatibility
    'username' => env('TARA_USERNAME'),
    'password' => env('TARA_PASSWORD'),
    'branch_code' => env('TARA_BRANCH_CODE'),
    'logging' => env('TARA_LOGGING'),
];
