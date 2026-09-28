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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],
    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => App\Helpers\AppleJwtGenerator::generate(),
        'redirect' => env('APPLE_REDIRECT_URI'),
        'team_id' => env('APPLE_TEAM_ID'),
        'key_id' => env('APPLE_KEY_ID'),
        'private_key' => env('APPLE_PRIVATE_KEY'), // Ensure this is loaded correctly
    ],

    'bunny_khlup' => [
        'storage_zone' => env('BUNNY_KHLUP_STORAGE_ZONE', 'alphaneonstudioz'),
        'access_key' => env('BUNNY_KHLUP_ACCESS_KEY', '483afefd-9e1a-42a0-be8e9032335f-3db5-46eb'),
        'endpoint' => env('BUNNY_KHLUP_ENDPOINT', 'https://storage.bunnycdn.com/alphaneonstudioz'),
        'cdn_url' => env('BUNNY_KHLUP_CDN_URL', 'https://alphaneon.b-cdn.net'),
    ],

];
