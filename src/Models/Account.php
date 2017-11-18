<?php

namespace Amelia\Monzo\Models;

/**
 * Account model.
 *
 * @property string $id The account's ID.
 * @property string $description The account description.
 * @property string $type Currently either "uk_prepaid" or "uk_retail" for most.
 * @property string $account_number UK account number, if a current account.
 * @property string $sort_code UK sort code, if a current account.
 * @property \Carbon\Carbon $created The date the account was created.
 */
class Account extends Model
{
    /**
     * Casts for this model.
     *
     * @var array
     */
    protected $casts = ['created' => 'date'];
}
