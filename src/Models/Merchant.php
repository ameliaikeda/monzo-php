<?php

namespace Amelia\Monzo\Models;

/**
 * Merchant model.
 *
 * @property string $id
 * @property string $emoji
 * @property string $name
 * @property string $category
 * @property string $group_id
 * @property string $logo
 * @property \Amelia\Monzo\Models\Address $address
 */
class Merchant extends Model
{
    /**
     * Casts for this model.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'date',
        'address' => Address::class,
    ];
}
