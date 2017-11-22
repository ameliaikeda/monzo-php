<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Webhook;

trait Webhooks
{
    /**
     * Get a user's webhooks.
     *
     * @param string|null $account
     * @return \Amelia\Monzo\Models\Webhook[]|\Illuminate\Support\Collection
     */
    public function webhooks(string $account = null)
    {
        $results = $this->call('GET', 'webhooks', [
            'account_id' => $account ?? $this->getAccountId(),
        ], [], 'webhooks');

        return collect($results)->map(function ($item) {
            return new Webhook($item, $this);
        });
    }

    /**
     * Get an individual webhook by ID.
     *
     * @param string $id
     * @return \Amelia\Monzo\Models\Webhook
     */
    public function webhook(string $id)
    {
        $result = $this->call('GET', "webhooks/$id", [], [], 'webhooks');

        return new Webhook($result, $this);
    }

    /**
     * Delete a webhook by ID.
     *
     * @param string $id
     * @return void
     */
    public function deleteWebhook(string $id)
    {
        $this->call('DELETE', "webhooks/$id");
    }

    /**
     * Register a webhook to an optional account.
     *
     * @param string $url
     * @param string|null $account
     * @return \Amelia\Monzo\Models\Webhook
     */
    public function registerWebhook(string $url, string $account = null)
    {
        $result = $this->call('POST', 'webhooks', [], [
            'url' => $url,
            'account_id' => $account ?? $this->getAccountId(),
        ], 'webhook');

        return new Webhook($result, $this);
    }
}
