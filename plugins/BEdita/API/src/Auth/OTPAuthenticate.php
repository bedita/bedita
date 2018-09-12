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

use BEdita\Core\State\CurrentApplication;
use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validation;

/**
 * Authenticate users via One Time Password.
 *
 * @since 4.0.0
 */
class OTPAuthenticate extends BaseAuthenticate
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
     *
     * @var array
     */
    protected $_defaultConfig = [
        'authProviders' => [],
        'fields' => [
            'username' => 'username',
            'password' => null,
        ],
        'userModel' => 'Users',
        'finder' => 'all',
        'expiry' => '+15 minutes',
    ];

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $username = $request->getData('username');
        if (empty($username)) {
            return false;
        }

        $grant = $request->getData('grant_type');
        if ($grant === 'otp') {
            return $this->otpAccess($username, $request);
        } elseif ($grant === 'otp_request') {
            return $this->otpRequest($username, $request);
        }

        return false;
    }

    /**
     * Retrieve access grant using authorization code and secret token.
     *
     * @param string $username User name
     * @param ServerRequest $request Request object
     * @return array|bool User data array on success, false on failure
     */
    protected function otpAccess($username, ServerRequest $request)
    {
        $result = $this->_findUser($username);
        if (empty($result)) {
            return false;
        }

        if (empty($request->getData('authorization_code')) || empty($request->getData('token'))) {
            return false;
        }

        $data = [
            'user_id' => $result['id'],
            'application_id' => CurrentApplication::getApplicationId(),
            'client_token' => $request->getData('authorization_code'),
            'secret_token' => $request->getData('token'),
            'token_type' => 'otp',
        ];

        $UserTokens = TableRegistry::get('UserTokens');
        $userToken = $UserTokens->find()->where($data)->first();
        if (!empty($userToken)) {
            $UserTokens->deleteOrFail($userToken);

            return $result;
        }

        return false;
    }

    /**
     * Generate a new client and secret token upon `otp_request`
     *
     * @param string $username User name
     * @param ServerRequest $request Request object
     * @return array|bool Authorization code array on success, false on failure
     */
    protected function otpRequest($username, ServerRequest $request)
    {
        $result = $this->_findUser($username);
        if (empty($result)) {
            return false;
        }

        $data = [
            'user_id' => $result['id'],
            'application_id' => CurrentApplication::getApplicationId(),
            'client_token' => $this->generateClientToken(),
            'secret_token' => $this->generateSecretToken(),
            'token_type' => 'otp',
            'expires' => new Time($this->getConfig('expiry')),
        ];

        $UserTokens = TableRegistry::get('UserTokens');
        $entity = $UserTokens->newEntity($data);
        $UserTokens->saveOrFail($entity);

        return ['authorization_code' => $data['client_token']];
    }

    /**
     * Generate authorization code, aka client token
     *
     * @return string The generated token
     */
    protected function generateClientToken()
    {
        return Text::uuid();
    }

    /**
     * Generate secret token, to be sent separately in a secure way to user
     *
     * @return string The generated secure token
     */
    protected function generateSecretToken()
    {
        return '0123456';
    }
}
