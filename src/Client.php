<?php

namespace Amelia\Monzo;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as Guzzle;
use Amelia\Monzo\Exceptions\MonzoException;
use Amelia\Monzo\Exceptions\NotFoundException;
use Amelia\Monzo\Exceptions\AccessTokenExpired;
use Amelia\Monzo\Exceptions\RateLimitException;
use Amelia\Monzo\Exceptions\AccessDeniedException;
use Amelia\Monzo\Exceptions\EmptyResponseException;
use Amelia\Monzo\Contracts\Client as ClientContract;
use Amelia\Monzo\Exceptions\AuthenticationException;
use Amelia\Monzo\Exceptions\InvalidRequestException;
use Amelia\Monzo\Exceptions\UnexpectedValueException;

class Client implements ClientContract
{
    /**
     * The guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * The API token we're using.
     *
     * @var string
     */
    protected $token;

    /**
     * Monzo client ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Monzo client secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Default query params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * If we should persist params across a single request.
     *
     * @var bool
     */
    protected $persist = false;

    /**
     * Create a new client instance.
     *
     * @param \GuzzleHttp\Client $guzzle
     * @param string|null $id
     * @param string|null $secret
     */
    public function __construct(Guzzle $guzzle, string $id = null, string $secret = null)
    {
        $this->guzzle = $guzzle;
        $this->id = $id;
        $this->secret = $secret;
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
     * @return mixed
     */
    public function call(string $method, string $endpoint, string $key = null, array $query = [], array $data = [], bool $raw = false)
    {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'base_uri' => static::API_ENDPOINT,
            'http_errors' => false,
        ];

        if ($this->token) {
            $options['headers']['Authorization'] = 'Bearer '.$this->token;
        }

        if ($params = ($this->params + $query)) {
            $options['query'] = $params;
        }

        if ($data) {
            $options['form_params'] = $data;
        }

        $response = $this->guzzle->request($method, $endpoint, $options);

        $this->clear();

        return $raw ? $response : $this->parse($response, $key);
    }

    /**
     * Clear all auth tokens/etc on this client instance.
     *
     * @return void
     */
    protected function clear()
    {
        $this->token = null;

        if ($this->persist) {
            $this->persist = false;
        } else {
            $this->params = [];
        }
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
        if ($response->getStatusCode() !== 200) {
            $this->handleGenericErrors($response);
        }

        $type = $response->getHeaderLine('Content-Type');

        if ($type !== 'application/json') {
            throw new UnexpectedValueException("Expected application/json, got $type");
        }

        $body = json_decode($response->getBody()->getContents(), true);

        if ($body === null) {
            throw new EmptyResponseException('Empty response given');
        }

        if ($key !== null && ! array_key_exists($key, $body)) {
            throw new UnexpectedValueException("Expected to find a [$key] key within the response; none found.");
        }

        return $key === null ? $body : $body[$key];
    }

    /**
     * Handle any generic http error code exceptions.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @return void
     * @throws \Amelia\Monzo\Exceptions\InvalidRequestException
     * @throws \Amelia\Monzo\Exceptions\AccessDeniedException
     * @throws \Amelia\Monzo\Exceptions\AccessTokenExpired
     * @throws \Amelia\Monzo\Exceptions\AuthenticationException
     * @throws \Amelia\Monzo\Exceptions\MonzoException
     * @throws \Amelia\Monzo\Exceptions\RateLimitException
     * @throws \Amelia\Monzo\Exceptions\NotFoundException
     */
    protected function handleGenericErrors(Response $response)
    {
        switch ($code = $response->getStatusCode()) {
            case 400:
                throw new InvalidRequestException('Bad request');
            case 401:
                $body = json_decode($response->getBody()->getContents(), true);

                if (is_array($body)
                    && isset($body['error'])
                    && $body['error'] === 'invalid_token'
                ) {
                    throw new AccessTokenExpired;
                }

                throw new AuthenticationException('You are not authenticated.');
            case 403:
                throw new AccessDeniedException('You are authenticated but not authorized to make this action.');
            case 404:
                throw new NotFoundException;
            case 405:
                throw new InvalidRequestException('Invalid HTTP verb for this endpoint');
            case 406:
                throw new InvalidRequestException('Your application does not accept the content format returned according to the Accept headers sent in the request.');
            case 429:
                throw new RateLimitException('You\'ve exceeded your rate limit; back off');
            case 500:
            case 504:
                throw new MonzoException('Monzo\'s API just errored internally. Check https://status.monzo.com for more info.');
            default:
                throw new MonzoException("Unknown error code: $code");
        }
    }

    /**
     * Set the token for this request.
     *
     * @param string $token
     * @return ClientContract
     */
    public function token(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Issue an oauth request with client params set.
     *
     * @param string $refreshToken
     * @return array an array with refresh_token and access_token values.
     */
    public function refresh(string $refreshToken)
    {
        $response = $this->call('POST', 'oauth2/token', '', [], [
            'client_id' => $this->id,
            'client_secret' => $this->secret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ], true);

        $result = json_decode($response->getBody()->getContent(), true);

        if (isset($result['error'])) {
            throw new AuthenticationException($result['error'].' '.($result['hint'] ?? ''));
        }

        return $result;
    }

    /**
     * Set the client ID.
     *
     * @param string $id
     * @return void
     */
    public function setClientId(string $id)
    {
        $this->id = $id;
    }

    /**
     * Set the client secret.
     *
     * @param string $secret
     * @return void
     */
    public function setClientSecret(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Set the "since" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function since(string $id)
    {
        $this->params['since'] = $id;
    }

    /**
     * Set the "before" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function before(string $id)
    {
        $this->params['before'] = $id;
    }

    /**
     * Set the max number of results to return from the API.
     *
     * @param int $limit
     * @return void
     */
    public function take(int $limit)
    {
        $this->params['limit'] = $limit;
    }

    /**
     * Expand a given key in the response.
     *
     * @param array $params
     * @return void
     */
    public function expand(array $params)
    {
        $this->params['expand'] = $params;
    }

    /**
     * Persist params for a single request.
     *
     * @return void
     */
    public function persist()
    {
        $this->persist = true;
    }

    /**
     * Return the currently set query parameters.
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * Set the query parameters for this request.
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
}
