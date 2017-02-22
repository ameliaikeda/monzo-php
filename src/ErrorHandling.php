<?php

namespace Amelia\Monzo;

use Closure;
use Amelia\Monzo\Exceptions\AccessTokenExpired;

trait ErrorHandling
{
    /**
     * Run an API call with error handling in case our access token is expired.
     *
     * @param \Closure $callback
     * @return mixed
     */
    protected function withErrorHandling(Closure $callback)
    {
        try {
            return $callback();
        } catch (AccessTokenExpired $e) {
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
}
