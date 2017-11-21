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
        $results = $this->withErrorHandling(function () use ($account) {
            if ($account === null) {
                // best-effort to find an existing account.

                $account = $this->getAccountId();
            }

            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('GET', 'webhooks', ['account_id' => $account], [], 'webhooks');
        });

        return collect($results)->map(function ($item) {
            return new Webhook($item);
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
        $result = $this->withErrorHandling(function () use ($id) {
            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('GET', "webhooks/$id", [], [], 'webhooks');
        });

        return new Webhook($result);
    }

    /**
     * Delete a webhook by ID.
     *
     * @param string $id
     * @return void
     */
    public function deleteWebhook(string $id)
    {
        $this->withErrorHandling(function () use ($id) {
            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('DELETE', "webhooks/$id");
        });
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
        $result = $this->withErrorHandling(function () use ($url, $account) {
            if ($account === null) {
                // best-effort to find an existing account.

                $account = $this->getAccountId();
            }

            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('POST', 'webhooks', [], [
                    'url' => $url,
                    'account_id' => $account,
                ], 'webhook');
        });

        return new Webhook($result);
    }
}
