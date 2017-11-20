<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'id' => getenv('MONZO_CLIENT_ID') ?: null,
    'secret' => getenv('MONZO_CLIENT_SECRET') ?: null,
    'redirect' => getenv('MONZO_REDIRECT_URI') ?: null,

    'webhooks' => [
        'active' => getenv('MONZO_WEBHOOKS') === 'true',
        'model' => getenv('MONZO_USER_MODEL') ?: 'App\\User',
        'user_token' => getenv('MONZO_USER_TOKEN_KEY') ?: 'monzo_user_token',
        'token' => getenv('MONZO_WEBHOOK_TOKEN_KEY') ?: 'monzo_webhook_token',
    ],
];
