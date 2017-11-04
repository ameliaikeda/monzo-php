<?php

namespace Amelia\Monzo\Exceptions;

class InvalidTokenException extends AuthenticationException
{
    /**
     * The status code for this exception.
     *
     * @var int
     */
    protected static $status = 401;

    /**
     * The status message for this exception.
     *
     * @var string
     */
    protected static $statusMessage = 'Your access token is invalid or expired.';
}
