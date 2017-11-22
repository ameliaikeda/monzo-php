<?php

namespace Amelia\Monzo\Api;

use Closure;
use Amelia\Monzo\Exceptions\InvalidTokenException;

trait ErrorHandling
{
    /**
     * Run an API call with error handling in case our access token is expired.
     *
     * @param \Closure $callback
     * @return mixed
     */
    protected function retry(Closure $callback)
    {
        try {
            return $callback();
        } catch (InvalidTokenException $e) {
            if ($this->hasRefreshToken()) {
                $this->refresh();

                return $callback();
            }

            throw $e;
        }
    }

    /**
     * Refresh the credentials for this request.
     *
     * @return array
     */
    public function refresh()
    {
        $result = $this->client->refresh($this->getRefreshToken());

        $token = $result['access_token'];
        $refreshToken = $result['refresh_token'];

        if ($this->hasUser()) {
            $user = $this->getUser();

            $user->updateMonzoCredentials($token, $refreshToken);
        }

        $this->token = $token;
        $this->refreshToken = $refreshToken;

        return $result;
    }

    /**
     * Call a specific endpoint on the client, with retrying for access tokens.
     *
     * @param string $method The method we want to use (GET, POST, PUT, DELETE, PATCH)
     * @param string $endpoint The endpoint we want to hit.
     * @param array $query The query params we're going to send.
     * @param array $data The form params we're going to send (if non-GET)
     * @param null|string $key The key we're expecting inside of this method
     * @param bool $new If a new client should be used or not.
     * @return array
     */
    public function call(string $method, string $endpoint, array $query = [], array $data = [], ?string $key = null, bool $new = true)
    {
        return $this->retry(function () use ($method, $endpoint, $query, $data, $key, $new) {
            return $this->client($new)
                ->token($this->getAccessToken())
                ->call($method, $endpoint, $query, $data, $key);
        });
    }

    /**
     * Get a client instance.
     *
     * @param bool $new
     * @return \Amelia\Monzo\Contracts\Client
     */
    protected function client(bool $new = true)
    {
        return $new ? $this->client->newClient() : $this->client;
    }
}
