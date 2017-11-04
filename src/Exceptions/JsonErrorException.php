<?php

namespace Amelia\Monzo\Exceptions;

class JsonErrorException extends MonzoException
{
    /**
     * The raw input given to json_decode
     *
     * @var mixed
     */
    public $raw;

    /**
     * The raw error from {@see json_last_error_msg()}
     *
     * @var string
     */
    public $error;

    /**
     * The status message for this error.
     *
     * @var int
     */
    protected static $status = 500;

    /**
     * JsonErrorException constructor.
     *
     * @param string $error
     * @param mixed $raw
     */
    public function __construct(string $error, $raw)
    {
        $this->raw = $raw;
        $this->error = $error;

        parent::__construct("{$this->error}\nraw body: {$this->raw}");
    }
}
