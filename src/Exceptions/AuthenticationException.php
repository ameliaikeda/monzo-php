<?php

namespace Amelia\Monzo\Exceptions;

class AuthenticationException extends MonzoException
{
    /**
     * The HTTP Status code for this error.
     *
     * @var int
     */
    protected static $status = 403;

    /**
     * The message for this exception.
     *
     * @var string
     */
    protected static $statusMessage = 'You are not authenticated.';
}
