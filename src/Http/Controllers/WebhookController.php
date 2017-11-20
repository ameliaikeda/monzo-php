<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Amelia\Monzo\Models\Transaction;
use Amelia\Monzo\Events\TransactionCreated;
use Amelia\Monzo\Exceptions\MonzoException;
use Illuminate\Routing\Controller as BaseController;

class WebhookController extends BaseController
{
    /**
     * @param string $token
     * @param \Illuminate\Http\Request $request
     */
    public function hook(string $token, Request $request)
    {
        if (app()->environment('local')) {
            logger(json_encode($request->all(), JSON_PRETTY_PRINT));
        }

        $user = $this->findByWebhookToken($token);

        $type = $request->input('type');

        if ($type === 'transaction.created') {
            event(new TransactionCreated(new Transaction($request->input('data')), $user));
        } else {
            logger('Unhandled webhook type: ' . $type);
        }
    }

    /**
     * Get a user model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getUserModel()
    {
        $model = config('monzo.webhooks.model', 'App\\User');

        if (! class_exists($model)) {
            throw new MonzoException('Class ' . $model . ' not found while processing a webhook.');
        }

        return new $model;
    }

    /**
     * Get a user by webhook token.
     *
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function findByWebhookToken(string $token)
    {
        // todo: use two tokens + hash_equals to avoid a timing attack on the latter one.
        $model = $this->getUserModel();

        $user = $model->newQuery()
            ->where(config('monzo.webhooks.attribute', 'webhook_token'), $token)
            ->first();

        if ($user === null) {
            logger('User not found for webhook token: ' . $token);
        }

        return $user;
    }
}
