<?php

namespace Amelia\Monzo\Exceptions;

class MethodNotAllowedException extends InvalidRequestException
{
    /**
     * The HTTP Status for this error.
     *
     * @var int
     */
    protected static $status = 405;

    /**
     * The status message for this error.
     *
     * @var string
     */
    protected static $statusMessage = 'You are using an incorrect HTTP verb. Double check whether it should be POST/GET/DELETE, etc.';
}
