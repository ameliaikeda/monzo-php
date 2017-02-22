<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Api\{Accounts, Balance, Transactions};
use Amelia\Monzo\Contracts\Client as ClientContract;
use Amelia\Monzo\Contracts\HasMonzoCredentials;
use Amelia\Monzo\Exceptions\MonzoException;
use Laravel\Socialite\Two\User;
use TypeError;

class Monzo
{
    use ErrorHandling, Accounts, Transactions, Balance;

    /**
     * A user's access token.
     *
     * @var string
     */
    protected $token;

    /**
     * The refresh token for the API.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * The account to use for querying.
     *
     * @var string
     */
    protected $account;

    /**
     * An API client to use.
     *
     * @var \Amelia\Monzo\Contracts\Client
     */
    protected $client;

    /**
     * A user, if given.
     *
     * @var \Amelia\Monzo\Contracts\HasMonzoCredentials
     */
    protected $user;

    /**
     * A default user, if given.
     *
     * @var \Amelia\Monzo\Contracts\HasMonzoCredentials
     */
    protected static $defaultUser;

    /**
     * Default access token, if set.
     *
     * @var string
     */
    protected static $defaultToken;

    /**
     * Default refresh token, if set.
     *
     * @var string
     */
    protected static $defaultRefreshToken;

    /**
     * Make a new monzo instance.
     *
     * @param \Amelia\Monzo\Contracts\Client $client
     */
    public function __construct(ClientContract $client)
    {
        $this->client = $client;
    }

    /**
     * Set the current user or access token.
     *
     * @param \Laravel\Socialite\Two\User|\Amelia\Monzo\Contracts\HasMonzoCredentials|string $user
     * @param string $refreshToken
     * @return $this
     */
    public function as($user, string $refreshToken = null)
    {
        // assume a raw access token was just passed in
        if (is_string($user)) {
            $this->token = $user;
            $this->refreshToken = $refreshToken;
        }

        // if we were given a socialite user, use that.
        elseif ($user instanceof User) {
            $this->token = $user->token;
            $this->refreshToken = $user->refreshToken;
        }

        // if we were given a proper user object, use that.
        elseif ($user instanceof HasMonzoCredentials) {
            $this->user = $user;
            $this->token = $user->getMonzoAccessToken();
            $this->refreshToken = $user->getMonzoRefreshToken();
        }

        return $this;
    }

    /**
     * Add the "before" pagination query param.
     *
     * @param string $id
     * @return $this
     */
    public function before(string $id)
    {
        $this->client->before($id);

        return $this;
    }

    /**
     * Add the "since" pagination query param.
     *
     * @param string $id
     * @return $this
     */
    public function since(string $id)
    {
        $this->client->since($id);

        return $this;
    }

    /**
     * Select the max number of objects to return from the API.
     *
     * @param int $limit
     * @return \Amelia\Monzo\Monzo
     */
    public function take(int $limit)
    {
        $this->client->take($limit > 100 ? 100 : $limit);

        return $this;
    }

    /**
     * Expand given keys.
     *
     * @param string|array $params
     * @return $this
     */
    public function expand($params)
    {
        $this->client->expand(is_array($params) ? $params : func_get_args());

        return $this;
    }

    /**
     * Set the current access token.
     *
     * @param string $token
     * @param string|null $refreshToken
     * @return void
     */
    public static function setToken(string $token, string $refreshToken = null)
    {
        static::$defaultToken = $token;
        static::$defaultRefreshToken = $refreshToken;
    }

    /**
     * Set credentials via a user object.
     *
     * @param \Laravel\Socialite\Two\User|\Amelia\Monzo\MonzoCredentials $user
     * @return void
     * @throws \TypeError
     */
    public static function setUser($user)
    {
        // if we've been passed a socialite user, use that.
        if ($user instanceof User) {
            static::$defaultToken = $user->token;
            static::$defaultRefreshToken = $user->refreshToken;
        }

        // If we're using the monzo interface, we can just use that.
        elseif ($user instanceof HasMonzoCredentials) {
            static::$defaultUser = $user;
            static::$defaultToken = $user->getMonzoAccessToken();
            static::$defaultRefreshToken = $user->getMonzoRefreshToken();
        }

        // if we didn't get either, throw a TypeError.
        else {
            throw new TypeError(
                static::class . '::' . __METHOD__ .
                ' expects ' . User::class . ' or an object using '
                . MonzoCredentials::class
            );
        }
    }

    /**
     * Get the current access token.
     *
     * @return string
     * @throws \Amelia\Monzo\Exceptions\MonzoException if an access token is not set.
     */
    protected function getAccessToken()
    {
        $token = $this->token ?? static::$defaultToken;

        if ($token === null) {
            throw new MonzoException(
                'An access token has not been set; '.
                'have you given a user?'
            );
        }

        return $token;
    }

    /**
     * @return string
     * @throws \Amelia\Monzo\Exceptions\MonzoException if a refresh token is not set.
     */
    protected function getRefreshToken()
    {
        $token = $this->refreshToken ?? static::$defaultRefreshToken;

        if ($token === null) {
            throw new MonzoException(
                'A refresh token has not been set; ' .
                'have you given a user, and are you using a confidential client?'
            );
        }

        return $token;
    }

    /**
     * Check if we have a refresh token set.
     *
     * @return bool
     */
    protected function hasRefreshToken()
    {
        return ($this->refreshToken ?? static::$defaultRefreshToken) !== null;
    }

    /**
     * Check if we have a user present.
     *
     * @return bool
     */
    protected function hasUser()
    {
        return $this->getUser() instanceof HasMonzoCredentials;
    }

    /**
     * Get a user, if set.
     *
     * @return \Amelia\Monzo\Contracts\HasMonzoCredentials
     */
    protected function getUser()
    {
        return $this->user ?? static::$defaultUser;
    }
}
