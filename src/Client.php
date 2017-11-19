<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Util\QueryParams;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as Guzzle;
use Amelia\Monzo\Exceptions\MonzoException;
use Amelia\Monzo\Contracts\Client as ClientContract;
use Amelia\Monzo\Exceptions\UnexpectedValueException;

class Client implements ClientContract
{
    use UsesTokens, UsesQueryParams;

    /**
     * The guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Create a new client instance.
     *
     * @param \GuzzleHttp\Client $guzzle
     * @param string|null $id
     * @param string|null $secret
     */
    public function __construct(Guzzle $guzzle, ?string $id = null, ?string $secret = null)
    {
        $this->guzzle = $guzzle;

        if ($id !== null) {
            $this->setClientId($id);
        }

        if ($secret !== null) {
            $this->setClientSecret($secret);
        }
    }

    /**
     * Do an API call.
     *
     * @param string $method
     * @param string $endpoint
     * @param string|null $key
     * @param array $query
     * @param array $data
     * @param bool $raw
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function call(string $method, string $endpoint, array $query = [], array $data = [], ?string $key = null, bool $raw = false)
    {
        $endpoint = $this->endpoint($endpoint, $query);

        $options = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ];

        if ($this->token) {
            $options['headers']['Authorization'] = 'Bearer '.$this->token;
        }

        if ($data) {
            $options['form_params'] = $data;
        }

        $response = $this->guzzle->request($method, $endpoint, $options);

        return $raw ? $response : $this->parse($response, $key);
    }

    /**
     * Get an endpoint (including query params).
     *
     * @param string $endpoint
     * @param array $query
     * @return string
     */
    protected function endpoint(string $endpoint, array $query)
    {
        $params = $this->buildQueryParams($query);

        return static::API_ENDPOINT . '/' . trim($endpoint, '/') . '?' . $params;
    }

    /**
     * Build query parameters for this call.
     *
     * @param array $query
     * @return string
     */
    protected function buildQueryParams(array $query)
    {
        $params = new QueryParams(array_merge($this->params, $query));

        return $params->build();
    }

    /**
     * Clear all auth tokens/etc on this client instance.
     *
     * @return void
     */
    protected function clear()
    {
        $this->token = null;
        $this->params = [];
    }

    /**
     * Parse out data from the API that we want.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @param string $key
     * @return array
     */
    protected function parse(Response $response, string $key = null)
    {
        $code = $response->getStatusCode();

        $body = $response->getBody()->getContents();

        $json = json_decode_response($response, $body);

        if ($code >= 400 || $code < 200) {
            $errorCode = array_get($json, 'error');

            throw MonzoException::fromResponse($response, $body, $errorCode);
        }

        if ($key !== null && ! array_key_exists($key, $json)) {
            throw new UnexpectedValueException("Expected to find a [$key] key within the response; none found.");
        }

        return $key === null ? $json : $json[$key];
    }

    /**
     * Get a new client instance.
     *
     * @return \Amelia\Monzo\Client
     */
    public function newClient()
    {
        return new static($this->guzzle, $this->id, $this->secret);
    }
}
