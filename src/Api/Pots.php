<?php

namespace Amelia\Monzo\Api;

use Amelia\Monzo\Models\Pot;
use Amelia\Monzo\Exceptions\MonzoException;

trait Pots
{
    /**
     * Get a user's pots.
     *
     * @return \Amelia\Monzo\Models\Pot[]|\Illuminate\Support\Collection
     */
    public function pots()
    {
        $results = $this->withErrorHandling(function () {
            return $this->client
                ->newClient()
                ->token($this->getAccessToken())
                ->call('GET', 'pots/listV1', [], [], 'pots');
        });

        return collect($results)->map(function ($item) {
            return new Pot($item, $this);
        });
    }

    /**
     * Get a pot by ID.
     *
     * @param string $id
     * @return void
     */
    public function pot(string $id)
    {
        throw new MonzoException('Getting a specific pot is not implemented in the dev API yet.');
    }

    /**
     * Fund a pot.
     *
     * @param string $id
     * @param int $amount
     * @param null|string $account
     * @return void
     */
    public function addToPot(string $id, int $amount, ?string $account = null)
    {
        throw new MonzoException('Adding to pots is not implemented in the dev API yet.');
    }

    /**
     * Withdraw a given amount from a pot.
     *
     * @param string $id
     * @param int $amount
     * @param null|string $account
     * @return void
     */
    public function withdrawFromPot(string $id, int $amount, ?string $account = null)
    {
        throw new MonzoException('Withdrawing from pots is not implemented in the dev API yet.');
    }

    /**
     * Update a pot (e.g. the style).
     *
     * @param string $pot
     * @param array $attributes
     * @return void
     */
    public function updatePot(string $pot, array $attributes)
    {
        throw new MonzoException('Updating pots is not implemented in the Developer API yet.');
    }

    /**
     * Delete a given pot.
     *
     * @param string $pot
     * @return void
     */
    public function deletePot(string $pot)
    {
        throw new MonzoException('Deleting pots is not implemented in the dev API yet.');
    }
}
