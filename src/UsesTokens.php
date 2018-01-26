<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Contracts\Client as ClientContract;

trait UsesTokens
{
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
     * The API token we're using.
     *
     * @var string
     */
    protected $token;

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
        $result = $this->newClient()->call('POST', 'oauth2/token', null, [], [
            'client_id' => $this->id,
            'client_secret' => $this->secret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        // set the access token to the new one
        $this->token($result['access_token']);

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
     * Get the client ID.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->id;
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
}
