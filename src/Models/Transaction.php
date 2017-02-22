<?php

namespace Amelia\Monzo\Models;

/**
 * Transaction model.
 *
 * @property string $id
 * @property string $description
 * @property string $currency
 * @property string $decline_reason
 * @property int $account_balance
 * @property int $amount
 * @property bool $is_load
 * @property bool $pending
 * @property bool $declined
 * @property array $metadata
 * @property \Carbon\Carbon $created
 * @property \Carbon\Carbon|null $settled
 * @property \Amelia\Monzo\Models\Merchant|string $merchant
 */
class Transaction extends Model
{
    /**
     * Casts for this model.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'date',
        'settled' => 'date',
        'merchant' => Merchant::class,
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'pending',
        'declined',
    ];

    /**
     * Checks if this transaction is currently pending.
     *
     * @return bool
     */
    public function getPendingAttribute()
    {
        return $this->settled === null;
    }

    /**
     * Checks if this transaction is currently pending.
     *
     * @return bool
     */
    public function getDeclinedAttribute()
    {
        return $this->decline_reason !== null;
    }
}
