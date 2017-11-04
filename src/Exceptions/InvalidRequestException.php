<?php

namespace Amelia\Monzo\Exceptions;

class InvalidRequestException extends MonzoException
{
    /**
     * The status code for this error.
     *
     * @var int
     */
    protected static $status = 400;

    /**
     * The status message for this error.
     *
     * @var string
     */
    protected static $statusMessage = 'Bad request body.';
}
