<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validation;

/**
 * Anonymously authenticate users by providing a UUID.
 *
 * Users can authenticate to the server by providing an UUID in the request headers:
 *
 * ```
 * Authorization: UUID 485fc381-e790-47a3-9794-1337c0a8fe68
 * ```
 *
 * @since 4.0.0
 */
class UuidAuthenticate extends BaseAuthenticate
{

    /**
     * Default config for this object.
     *
     * - `authProvider` The AuthProvider entity associated to this authentication component.
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
     * - `header` Where to retrieve a user's token.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'authProvider' => null,
        'fields' => [
            'username' => 'provider_username',
        ],
        'userModel' => 'Users',
        'scope' => [],
        'finder' => 'all',
        'contain' => null,
        'passwordHasher' => 'Default',
        'header' => [
            'name' => 'Authorization',
            'prefix' => 'UUID ',
        ],
        'message' => 'Missing or invalid token',
    ];

    /**
     * Find a user by UUID.
     *
     * @param string $username UUID.
     * @param null $password Password.
     * @return array|bool
     */
    protected function _findUser($username, $password = null)
    {
        $externalAuth = parent::_findUser($username, $password);
        if (!empty($externalAuth)) {
            return $externalAuth;
        }

        $authProvider = $this->_config['authProvider'];

        $Table = TableRegistry::get($this->_config['userModel']);
        $Table->dispatchEvent('Auth.externalAuth', compact('authProvider', 'username'));

        return parent::_findUser($username, $password);
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(ServerRequest $request)
    {
        $token = $this->getToken($request);
        if ($token) {
            return $this->_findUser($token);
        }

        return false;
    }

    /**
     * Obtain the token from request headers.
     *
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return bool|string
     */
    public function getToken(ServerRequest $request)
    {
        $header = $request->getHeaderLine($this->_config['header']['name']);
        if (!$header) {
            return false;
        }

        $prefix = $this->_config['header']['prefix'];
        if (strpos($header, $prefix) !== 0) {
            return false;
        }

        $token = substr($header, strlen($prefix));
        if (!Validation::uuid($token)) {
            return false;
        }

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        throw new UnauthorizedException($this->_config['message']);
    }
}
