<?php

namespace Amelia\Monzo\Contracts;

interface Client
{
    const API_ENDPOINT = 'https://api.monzo.com';

    /**
     * Do an API call.
     *
     * @param string $method
     * @param string $endpoint
     * @param string $key
     * @param array $query
     * @param array $data
     * @param bool $raw
     * @return mixed
     */
    public function call(string $method, string $endpoint, string $key = null, array $query = [], array $data = [], bool $raw = false);

    /**
     * Set the token for this request.
     *
     * @param string $token
     * @return $this
     */
    public function token(string $token);

    /**
     * Issue an oauth request with client params set.
     *
     * @param string $refreshToken
     * @return array an array with refresh_token and access_token values.
     */
    public function refresh(string $refreshToken);

    /**
     * Set the client ID.
     *
     * @param string $id
     * @return void
     */
    public function setClientId(string $id);

    /**
     * Set the client secret.
     *
     * @param string $secret
     * @return void
     */
    public function setClientSecret(string $secret);

    /**
     * Set the "since" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function since(string $id);

    /**
     * Set the "before" pagination query param.
     *
     * @param string $id
     * @return void
     */
    public function before(string $id);

    /**
     * Set the max number of results to return from the API.
     *
     * @param int $limit
     * @return void
     */
    public function take(int $limit);

    /**
     * Expand a given key in the response.
     *
     * @param array $params
     * @return void
     */
    public function expand(array $params);

    /**
     * Return the currently set query parameters.
     *
     * @return array
     */
    public function params();

    /**
     * Set the query parameters for this request.
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params);
}
