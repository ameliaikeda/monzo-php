<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Account;
use Amelia\Monzo\Exceptions\MonzoException;

trait Accounts
{
    /**
     * Get a list of accounts for the current user.
     *
     * @return \Illuminate\Support\Collection|\Amelia\Monzo\Models\Account[]
     */
    public function accounts()
    {
        $results = $this->withErrorHandling(function () {
            return $this->client
                ->token($this->getAccessToken())
                ->call('GET', 'accounts', 'accounts');
        });

        return collect($results)->map(function ($item) {
            return new Account($item);
        });
    }

    /**
     * Get an existing account ID.
     *
     * @return string
     */
    protected function findExistingAccount()
    {
        $params = $this->client->params();
        $this->client->setParams([]);

        $accounts = $this->accounts();

        $this->client->setParams($params);

        if ($accounts->count() === 1) {
            return $accounts->first()->id;
        }

        if ($accounts->count() === 0) {
            throw new MonzoException('The given user has no accounts. Did you use the correct email for auth?');
        }

        throw new MonzoException('A user has more than one account; please specify it in the transactions method.');
    }
}
