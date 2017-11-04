<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Exceptions\EmptyResponseException;
use Amelia\Monzo\Exceptions\JsonErrorException;
use Amelia\Monzo\Exceptions\UnexpectedValueException;
use Psr\Http\Message\ResponseInterface;

use function json_decode as base_json_decode;

/**
 * Deserialize JSON from Monzo's API.
 *
 * @param $json
 * @return array|null
 */
function json_decode($json)
{
    $decoded = base_json_decode($json, true, 128);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new JsonErrorException(json_last_error_msg(), $json);
    }

    return $decoded;
}

/**
 * Get json from a PSR object.
 *
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return array|null
 */
function json_decode_response(ResponseInterface $response)
{
    $type = $response->getHeaderLine('Content-Type');

    if (! str_is($type, 'application/*json')) {
        throw new UnexpectedValueException("Expected application/*json, got $type");
    }

    $result = json_decode($response->getBody()->getContents());

    if ($result === null) {
        throw new EmptyResponseException;
    }

    return $result;
}
