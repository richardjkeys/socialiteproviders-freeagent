<?php

namespace SocialiteProviders\FreeAgent;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'FREEAGENT';
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.freeagent.sandbox') ? 'https://api.sandbox.freeagent.com/v2/approve_app' : 'https://api.freeagent.com/v2/approve_app', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return config('services.freeagent.sandbox') ? 'https://api.sandbox.freeagent.com/v2/token_endpoint' : 'https://api.freeagent.com/v2/token_endpoint';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(config('services.freeagent.sandbox') ? 'https://api.sandbox.freeagent.com/v2/users/me' : 'https://api.freeagent.com/v2/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['url'],
            'email' => $user['email'],
//            'nickname' => $user['display_name'],
            'name'     => $user['first_name'] . " " . $user['last_name'],
            'avatar'   => null,
        ]);
    }

}
