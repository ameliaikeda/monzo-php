<?php

namespace Amelia\Monzo\Exceptions;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

use function Amelia\Monzo\json_decode_response;

class MonzoException extends RuntimeException
{
    /**
     * The JSON body of this exception, if any.
     *
     * @var array|null
     */
    public $body;

    /**
     * The response given, if any.
     *
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    public $response;

    /**
     * An array of error codes mapped to exception types.
     *
     * @var array
     */
    protected static $errors = [
        'unauthorized' => AuthenticationException::class,
        'unauthorized.bad_access_token' => InvalidTokenException::class,
        'forbidden*' => AccessDeniedException::class,
        'bad_request*' => InvalidRequestException::class,
    ];

    /**
     * An array of status codes mapped to exception types.
     *
     * @var array
     */
    protected static $codes = [
        400 => InvalidRequestException::class,
        401 => InvalidTokenException::class,
        403 => AccessDeniedException::class,
        404 => NotFoundException::class,
        405 => MethodNotAllowedException::class,
        429 => RateLimitException::class,
        500 => MonzoException::class,
        504 => GatewayTimeoutException::class,
    ];

    /**
     * The HTTP Status code for this error.
     *
     * @var int
     */
    protected static $status = 500;

    /**
     * The message for this exception.
     *
     * @var string
     */
    protected static $statusMessage = 'Internal error from Monzo. Check https://status.monzo.com for more info.';

    /**
     * AuthenticationException constructor.
     *
     * @param string|null $message
     */
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? static::$statusMessage, static::$status);
    }

    /**
     * Get the HTTP Status code for this error.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response ? $this->response->getStatusCode() : static::$status;
    }

    /**
     * Set the response on this exception.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $body
     * @return $this
     */
    public function response(ResponseInterface $response, string $body)
    {
        $this->response = $response;
        $this->body = json_decode_response($response, $body);
        // set error message?

        return $this;
    }

    /**
     * Generate an exception from a response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $body
     * @param string $errorCode
     * @return static
     */
    public static function fromResponse(ResponseInterface $response, string $body, ?string $errorCode)
    {
        $class = static::getExceptionType($errorCode, $response->getStatusCode());

        /** @var \Amelia\Monzo\Exceptions\MonzoException $exception */
        $exception = new $class;

        return $exception->response($response, $body);
    }

    /**
     * Get the exception class from an underlying error.
     *
     * @param null|string $errorCode
     * @param int $statusCode
     * @return string
     */
    protected static function getExceptionType(?string $errorCode, int $statusCode)
    {
        // first, check error code.
        if ($errorCode !== null) {
            $errors = collect(static::$errors);

            // exact match first, followed by pattern match.
            $class = $errors->get($errorCode)
                ?? $errors->first(function ($value, string $key) use ($errorCode) {
                    return str_is($key, $errorCode);
                });

            if ($class !== null) {
                return $class;
            }
        }

        // now, check for code directly.
        return static::$codes[$statusCode] ?? self::class;
    }
}
