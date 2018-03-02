<?php

namespace Amelia\Monzo\Exceptions;

class RateLimitException extends MonzoException
{
    /**
     * Our request limit.
     *
     * @var int
     */
    public $limit;

    /**
     * The number of requests remaining.
     *
     * @var int
     */
    public $remaining;

    /**
     * The number of seconds to wait before retrying.
     *
     * @var int
     */
    public $retryAfter;

    /**
     * Status code for this error.
     *
     * @var int
     */
    protected static $status = 404;

    /**
     * Status message for this error.
     *
     * @var string
     */
    protected static $statusMessage = 'Rate limit for the monzo API reached, backing off. See https://monzo.com/docs';
}
