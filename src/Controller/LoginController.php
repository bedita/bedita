<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Controller for `/login` endpoint.
 *
 * @since 4.0.0
 */
class LoginController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->Auth->config(
            'authenticate',
            [
                AuthComponent::ALL => [
                    'scope' => [
                        'blocked' => false,
                    ],
                    'contain' => ['Roles'],
                ],
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password_hash',
                    ],
                    'passwordHasher' => [
                        'className' => 'Fallback',
                        'hashers' => [
                            'Default',
                            'Weak' => ['hashType' => 'md5'],
                        ],
                    ],
                ],
                'BEdita/API.Jwt' => [
                    'queryDatasource' => true,
                ],
            ],
            false
        );
    }

    /**
     * Login with username and password.
     *
     * @return void
     * @throws \Cake\Network\Exception\UnauthorizedException Throws an exception if user credentials are invalid.
     */
    public function login()
    {
        $this->request->allowMethod('post');

        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException(__('Login not successful'));
        }

        $algorithm = Configure::read('Security.jwt.algorithm') ?: 'HS256';
        $duration = Configure::read('Security.jwt.duration') ?: '+2 hours';
        $claims = [
            'iss' => Router::fullBaseUrl(),
            'iat' => time(),
            'nbf' => time(),
        ];

        $jwt = JWT::encode(
            $user + $claims + ['exp' => strtotime($duration)],
            Security::salt(),
            $algorithm
        );
        $renew = JWT::encode(
            $claims + ['sub' => $user['id']],
            Security::salt(),
            $algorithm
        );

        $this->set('_serialize', []);
        $this->set('_meta', compact('jwt', 'renew'));
    }
}
