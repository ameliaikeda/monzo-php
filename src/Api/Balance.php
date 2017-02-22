<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Balance as BalanceModel;

trait Balance
{
    /**
     * Get a list of accounts for the current user.
     *
     * @param string $account
     * @return \Amelia\Monzo\Models\Balance
     */
    public function balance(string $account = null)
    {
        $results = $this->withErrorHandling(function () use ($account) {
            if ($account === null) {
                $account = $this->findExistingAccount();
            }

            return $this->client
                ->token($this->getAccessToken())
                ->call('GET', 'balance', null, ['account_id' => $account]);
        });

        return new BalanceModel($results);
    }
}
