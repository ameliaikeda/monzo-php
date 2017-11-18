<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Account;
use Amelia\Monzo\Exceptions\MonzoException;

trait Accounts
{
    /**
     * An account ID to use.
     *
     * @var string
     */
    protected $account;

    /**
     * Get a list of accounts for the current user.
     *
     * @return \Illuminate\Support\Collection|\Amelia\Monzo\Models\Account[]
     */
    public function accounts()
    {
        $results = $this->withErrorHandling(function () {
            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('GET', 'accounts', [], [], 'accounts');
        });

        dd($results);

        return collect($results)->map(function ($item) {
            return new Account($item);
        });
    }

    /**
     * Get an existing account ID.
     *
     * @return string
     */
    protected function getAccountId()
    {
        if ($this->account) {
            return $this->account;
        }

        $accounts = $this->accounts();

        $account = $accounts->first(function (Account $account) {
            return $account->type === 'uk_retail';
        });

        if ($account === null) {
            throw new MonzoException('The given user has no accounts. Did you use the correct email for auth?');
        }

        return $this->account = $account->id;
    }
}
