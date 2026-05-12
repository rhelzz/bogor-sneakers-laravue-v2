<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'tiktok' => [
        'username' => env('TIKTOK_USERNAME', 'bogorsneaker'),
        'client_key' => env('TIKTOK_CLIENT_KEY'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
        'access_token' => env('TIKTOK_ACCESS_TOKEN'),
        'api_base_url' => env('TIKTOK_API_BASE_URL', 'https://open.tiktokapis.com'),
        'rapidapi_key' => env('TIKTOK_RAPIDAPI_KEY'),
        'rapidapi_host' => env('TIKTOK_RAPIDAPI_HOST', 'tiktok-scraper7.p.rapidapi.com'),
        'rapidapi_url' => env('TIKTOK_RAPIDAPI_URL', 'https://tiktok-scraper7.p.rapidapi.com/user/info'),
        'follower_cache_ttl' => env('TIKTOK_FOLLOWER_CACHE_TTL', 900),
    ],

    'rajaongkir' => [
        'key' => env('RAJAONGKIR_API_KEY'),
        'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
        'supported_couriers' => [
            'jne' => ['name' => 'JNE', 'supports_awb' => true],
            'sicepat' => ['name' => 'SiCepat', 'supports_awb' => true],
            'sap' => ['name' => 'SAP Express', 'supports_awb' => true],
            'ninja' => ['name' => 'Ninja Xpress', 'supports_awb' => true],
            'jnt' => ['name' => 'J&T Express', 'supports_awb' => true],
            'tiki' => ['name' => 'TIKI', 'supports_awb' => true],
            'wahana' => ['name' => 'Wahana Express', 'supports_awb' => true],
            'pos' => ['name' => 'POS Indonesia', 'supports_awb' => true],
            'lion' => ['name' => 'Lion Parcel', 'supports_awb' => true],
        ],
    ],

];
