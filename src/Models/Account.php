<?php

namespace Amelia\Monzo\Models;

/**
 * Account model.
 *
 * @property string $id The account's ID.
 * @property string $description The account description.
 * @property \Carbon\Carbon $created The date the account was created.
 */
class Account extends Model
{
    /**
     * Casts for this model.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'date',
    ];
}
