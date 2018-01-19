<?php

namespace Amelia\Monzo\Models;

/**
 * A savings pot.
 *
 * @property string $id              The ID of this Pot.
 * @property string $name            The name of this pot.
 * @property int $balance            The amount in this pot.
 * @property string $currency        The currency of this pot, e.g. GBP.
 * @property string $style           The style of this pot, e.g. "raspberry".
 * @property \Carbon\Carbon $created The date this pot was created.
 * @property \Carbon\Carbon $updated The date this pot was last updated.
 * @property bool $deleted           If this pot has been deleted.
 */
class Pot extends Model
{
    /**
     * An array of costs for this model.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'date',
        'updated' => 'date',
    ];

    /**
     * Add a given amount to a pot.
     *
     * @param int $amount
     * @param string|null $account
     * @return \Amelia\Monzo\Models\Pot
     */
    public function deposit(int $amount, string $account = null)
    {
        return $this->monzo->addToPot($this->id, $amount, $account);
    }

    /**
     * Add a given amount to a pot.
     *
     * @param int $amount
     * @param string|null $account
     * @return \Amelia\Monzo\Models\Pot
     */
    public function withdraw(int $amount, string $account = null)
    {
        return $this->monzo->withdrawFromPot($this->id, $amount, $account);
    }

    /**
     * Update a pot.
     *
     * @param array $attributes
     * @return \Amelia\Monzo\Models\Pot
     */
    public function update(array $attributes)
    {
        return $this->monzo->updatePot($this->id, $attributes);
    }
}
