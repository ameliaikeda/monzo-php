<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::post('webhooks/{user}/{token}', WebhookController::class . '@hook')
    ->name('monzo::webhook');
