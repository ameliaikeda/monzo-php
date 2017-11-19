<?php

namespace Amelia\Monzo\Exceptions;

class GatewayTimeoutException extends MonzoException
{
    /**
     * The status code.
     *
     * @var int
     */
    protected static $status = 504;

    /**
     * @var string
     */
    protected static $statusMessage = 'Gateway timeout from Monzo. See https://status.monzo.com for more info.';
}
