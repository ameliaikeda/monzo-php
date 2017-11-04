<?php

namespace Amelia\Monzo\Exceptions;

class NotFoundException extends MonzoException
{
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
    protected static $statusMessage = 'Endpoint not found. See https://monzo.com/docs';
}
