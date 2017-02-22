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
];
