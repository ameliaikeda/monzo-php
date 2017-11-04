<?php

namespace Amelia\Monzo\Exceptions;

class AccessDeniedException extends AuthenticationException
{
    /**
     * The HTTP Status code for this error.
     *
     * @var int
     */
    protected static $status = 401;

    /**
     * The message for this exception.
     *
     * @var string
     */
    protected static $statusMessage = 'Access Denied.';
}
