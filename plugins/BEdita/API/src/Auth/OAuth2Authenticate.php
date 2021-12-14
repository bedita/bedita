<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Auth;

use BEdita\Core\Utility\OAuth2;
use Cake\Auth\BaseAuthenticate;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * Authenticate users via OAuth2 providers.
 *
 * @since 4.0.0
 */
class OAuth2Authenticate extends BaseAuthenticate
{
    /**
     * Default config for this object.
     *
     * - `authProviders` The AuthProviders entities associated to this authentication component.
     *      Array formatted with `auth_providers.name` as key, from `AuthProvidersTable::findAuthenticate()`
     * - `fields` The fields to use to identify a user by.
     * - `userModel` The alias for users table, defaults to Users.
     * - `finder` The finder method to use to fetch user record. Defaults to 'all'.
     *   You can set finder name as string or an array where key is finder name and value
     *   is an array passed to `Table::find()` options.
     *   E.g. ['finderName' => ['some_finder_option' => 'some_value']]
     * - `passwordHasher` Password hasher class. Can be a string specifying class name
     *    or an array containing `className` key, any other keys will be passed as
     *    config to the class. Defaults to 'Default'.
     * - Options `scope` and `contain` have been deprecated since 3.1. Use custom
     *   finder instead to modify the query to fetch user record.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'authProviders' => [],
        'userModel' => 'Users',
        'fields' => [
            'username' => 'ExternalAuth.provider_username',
            'password' => null,
        ],
        'finder' => null,
        'passwordHasher' => 'Default',
    ];

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $data = $request->getData();
        if (empty($data['auth_provider']) || empty($data['provider_username']) || empty($data['access_token'])) {
            return false;
        }

        /** @var array $authProviders */
        $authProviders = $this->getConfig('authProviders');
        if (empty($authProviders[$data['auth_provider']])) {
            return false;
        }
        /** @var \BEdita\Core\Model\Entity\AuthProvider $authProvider */
        $authProvider = $authProviders[$data['auth_provider']];
        $options = (array)Hash::get((array)$authProvider->get('params'), 'options');
        $providerResponse = $this->getOAuth2Response(
            $authProvider->get('url'),
            $data['access_token'],
            $options
        );
        if (!$authProvider->checkAuthorization($providerResponse, $data['provider_username'])) {
            return false;
        }

        $this->setConfig('finder', [
            'externalAuth' => [
                'auth_provider' => $authProvider
            ]
        ]);

        return $this->_findUser($data['provider_username']);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(ServerRequest $request)
    {
        return false;
    }

    /**
     * Get response from an OAuth2 provider
     *
     * @param string $url OAuth2 provider URL
     * @param string $accessToken Access token to use in request
     * @param array $options OAuth2 request options
     * @return array Response from an OAuth2 provider
     * @codeCoverageIgnore
     */
    protected function getOAuth2Response(string $url, string $accessToken, array $options = []): array
    {
        return (new OAuth2())->response($url, $accessToken, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        $message = $this->_registry->getController()->Auth->getConfig('authError');
        throw new UnauthorizedException($message);
    }
}
