<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Balance as BalanceModel;

trait Balance
{
    /**
     * Get a balance for the current user.
     *
     * @param string $account
     * @return \Amelia\Monzo\Models\Balance
     */
    public function balance(string $account = null)
    {
        $results = $this->withErrorHandling(function () use ($account) {
            if (is_null($account = $account ?? $this->account)) {
                $account = $this->getAccountId();
            }

            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('GET', 'balance', ['account_id' => $account]);
        });

        return new BalanceModel($results);
    }
}
