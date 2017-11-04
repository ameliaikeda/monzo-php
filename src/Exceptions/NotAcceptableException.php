<?php

namespace Amelia\Monzo\Exceptions;

class NotAcceptableException extends InvalidRequestException
{
    /**
     * The HTTP Status for this error.
     *
     * @var int
     */
    protected static $status = 406;

    /**
     * The status message for this error.
     *
     * @var string
     */
    protected static $statusMessage = 'Wrong Accept Headers received (hint: reconfigure your client to use application/json). See https://monzo.com/docs/#errors';
}
