<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Transaction;

trait Transactions
{
    /**
     * Get a list of transactions for the current user.
     *
     * @param string|null $account
     * @return \Amelia\Monzo\Models\Transaction[]|\Illuminate\Support\Collection
     */
    public function transactions(string $account = null)
    {
        $results = $this->expand('merchant')
            ->call('GET', 'transactions', [
                'account_id' => $account ?? $this->getAccountId(),
            ], [], 'transactions', false);

        return collect($results)->map(function ($item) {
            return new Transaction($item, $this);
        });
    }

    /**
     * Get a single transaction for a given ID.
     *
     * @param string $id
     * @return \Amelia\Monzo\Models\Transaction
     */
    public function transaction(string $id)
    {
        $results = $this->expand('merchant')
            ->call('GET', "transactions/{$id}", [], [], 'transaction', false);

        return new Transaction($results, $this);
    }
}
