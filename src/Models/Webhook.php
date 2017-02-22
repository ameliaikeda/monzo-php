<?php

namespace Amelia\Monzo\Models;

/**
 * Webhook model.
 *
 * @property string $id
 * @property string $url
 * @property \Carbon\Carbon $created
 */
class Webhook extends Model
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
