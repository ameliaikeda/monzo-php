<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function hook(string $user, string $token, Request $request)
    {
        if (app()->environment('local')) {
            logger(json_encode($request->all(), JSON_PRETTY_PRINT));
        }

        $user = $this->findByWebhookToken($user, $token);

        $type = $request->input('type');

        if ($type === 'transaction.created') {
            event(new TransactionCreated(new Transaction($request->input('data')), $user));
        } else {
            logger("Unhandled webhook type: $type");
        }
    }

    /**
     * Get a user model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getUserModel()
    {
        if (! class_exists($model = config('monzo.webhooks.model'))) {
            throw new MonzoException('Class ' . $model . ' not found while processing a webhook.');
        }

        return new $model;
    }

    /**
     * Get a user by webhook token.
     *
     * @param string $user
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function findByWebhookToken(string $user, string $token)
    {
        $model = $this->getUserModel();

        $user = $model->newQuery()
            ->where(config('monzo.webhooks.user_token'), $user)
            ->first();

        if ($user === null) {
            logger("User not found for webhook token: $user");

            abort(404);
        }

        // now validate the token.
        $userToken = $user->getAttribute(config('monzo.webhooks.token'));

        if (! hash_equals($userToken, $token)) {
            logger("Hash equals failed for user: " . $token);

            abort(404);
        }

        return $user;
    }
}
