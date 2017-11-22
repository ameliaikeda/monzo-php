<?php

namespace Amelia\Monzo\Api;

trait Feed
{
    /**
     * Create a new feed item.
     *
     * @param string $title
     * @param array $params
     * @param string|null $url
     * @param string|null $account
     */
    public function createFeedItem(string $title, array $params, string $url = null, string $account = null)
    {
        $this->call('POST', 'feed', [], [
            'account_id' => $account ?? $this->getAccountId(),
            'title' => $title,
            'url' => $url,
            'params' => $params,
            'type' => 'basic',
        ]);
    }
}
