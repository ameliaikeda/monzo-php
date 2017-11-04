<?php

namespace Amelia\Monzo\Exceptions;

class EmptyResponseException extends MonzoException
{
    /**
     * The status code for this error.
     *
     * @var int
     */
    protected static $status = 500;

    /**
     * The status message for this error.
     *
     * @var string
     */
    protected static $statusMessage = 'Empty response from Monzo. See https://status.monzo.com if there are any ongoing issues.';
}
