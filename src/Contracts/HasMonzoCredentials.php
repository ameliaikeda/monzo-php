<?php

namespace Amelia\Monzo\Contracts;

use Laravel\Socialite\Two\User;

interface HasMonzoCredentials
{
    /**
     * Set a user's credentials from a user object returned via Socialite.
     *
     * @param \Laravel\Socialite\Two\User $user
     * @return $this
     */
    public function setMonzoUser(User $user);

    /**
     * Update this object to set new credentials.
     *
     * @param string $token
     * @param string $refreshToken
     * @return void
     */
    public function updateMonzoCredentials(string $token, string $refreshToken);

    /**
     * Get a monzo user's access token.
     *
     * @return string|null
     */
    public function getMonzoAccessToken();

    /**
     * Get a monzo user's refresh token.
     *
     * @return string|null
     */
    public function getMonzoRefreshToken();

    /**
     * Get a monzo user's refresh token.
     *
     * @return string|null
     */
    public function getMonzoUserId();
}
