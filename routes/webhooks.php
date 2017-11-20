<?php

use Illuminate\Support\Facades\Route;
use Amelia\Monzo\Http\Controllers\WebhookController;

Route::post('webhooks/{user}/{token}', WebhookController::class . '@handle')
    ->name('monzo::webhook');
