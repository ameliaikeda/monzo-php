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
        $results = $this->call('GET', 'balance', [
            'account_id' => $account ?? $this->getAccountId(),
        ]);

        return new BalanceModel($results, $this);
    }
}
