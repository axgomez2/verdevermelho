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
        'token' => env('POSTMARK_TOKEN'),
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

    'discogs' => [
    'token' => env('DISCOGS_TOKEN'),
    ],

    'youtube' => [
    'api_key' => env('YOUTUBE_API_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'melhorenvio' => [
        'client_id' => env('MELHORENVIO_CLIENT_ID'),
        'client_secret' => env('MELHORENVIO_CLIENT_SECRET'),
        'token' => env('MELHORENVIO_API_TOKEN'),
        'sandbox' => env('MELHORENVIO_SANDBOX', true),
        'from_postal_code' => env('MELHORENVIO_FROM_POSTAL_CODE'),
        'from_address' => env('MELHORENVIO_FROM_ADDRESS'),
        'from_number' => env('MELHORENVIO_FROM_NUMBER'),
        'from_complement' => env('MELHORENVIO_FROM_COMPLEMENT'),
        'from_district' => env('MELHORENVIO_FROM_DISTRICT'),
        'from_city' => env('MELHORENVIO_FROM_CITY'),
        'from_state' => env('MELHORENVIO_FROM_STATE'),
    ],
    
    // Configurações do WorkOS removidas
    
    'melhorenvio_shipping' => [
        'from_phone' => env('MELHORENVIO_FROM_PHONE'),
        'from_email' => env('MELHORENVIO_FROM_EMAIL'),
        'from_document' => env('MELHORENVIO_FROM_DOCUMENT'),
    ],

];
