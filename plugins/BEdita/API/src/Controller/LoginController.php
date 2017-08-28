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

use BEdita\Core\Model\Action\ChangeCredentialsAction;
use BEdita\Core\Model\Action\ChangeCredentialsRequestAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Controller for `/auth` endpoint.
 *
 * @since 4.0.0
 */
class LoginController extends AppController
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->request->contentType() === 'application/json') {
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }

        if ($this->request->getParam('action') === 'login') {
            $authenticationComponents = [
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
            ];

            $authenticationComponents += TableRegistry::get('AuthProviders')
                ->find('authenticate')
                ->toArray();

            $this->Auth->setConfig('authenticate', $authenticationComponents, false);
        }

        if ($this->request->getParam('action') === 'change') {
            $this->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);
        }
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

        if ($this->request->getData('password')) {
            $this->request = $this->request
                ->withData('password_hash', $this->request->getData('password'))
                ->withData('password', null);
        }

        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException(__('Login not successful'));
        }

        $user = $this->reducedUserData($user);
        $jwtMeta = $this->jwtTokens($user);

        $this->set('_serialize', []);
        $this->set('_meta', $jwtMeta);
    }

    /**
     * Return a reduced version of user data with only
     * `id`, `username` and for each role `id` and `name
     *
     * @param array $userInput Complete user data
     * @return array Reduced user data
     */
    protected function reducedUserData(array $userInput)
    {
        $roles = [];
        foreach ($userInput['roles'] as $role) {
            $roles[] = [
                'id' => $role['id'],
                'name' => $role['name'],
            ];
        }
        $user = array_intersect_key($userInput, array_flip(['id', 'username']));
        $user['roles'] = $roles;

        return $user;
    }

    /**
     * Calculate JWT token for auth and renew operations
     *
     * @param array $user Minimal user data to encode in JWT
     * @return array JWT tokens requested
     */
    protected function jwtTokens(array $user)
    {
        $algorithm = Configure::read('Security.jwt.algorithm') ?: 'HS256';
        $duration = Configure::read('Security.jwt.duration') ?: '+20 minutes';
        $currentUrl = Router::reverse($this->request, true);
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
            $claims + ['sub' => $user['id'], 'aud' => $currentUrl],
            Security::salt(),
            $algorithm
        );

        return compact('jwt', 'renew');
    }

    /**
     * Read logged user data.
     *
     * @return void
     */
    public function whoami()
    {
        $this->request->allowMethod('get');

        $user = $this->userEntity();

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Update user profile data.
     *
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException On invalid input data
     */
    public function update()
    {
        $this->request->allowMethod('patch');

        $entity = $this->userEntity();
        $entity->setAccess(['username', 'password', 'password_hash', 'email'], false);

        $data = $this->request->getData();
        $action = new SaveEntityAction(['table' => TableRegistry::get('Users')]);
        $action(compact('entity', 'data'));

        $entity = $this->userEntity();
        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);
    }

    /**
     * Read logged user entity.
     *
     * @return \Cake\Datasource\EntityInterface Logged user entity
     * @throws \Cake\Network\Exception\UnauthorizedException Throws an exception if user not logged.
     */
    protected function userEntity()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            $this->Auth->getAuthenticate('BEdita/API.Jwt')->unauthenticated($this->request, $this->response);
        }

        return TableRegistry::get('Users')->get($userId);
    }

    /**
     * Change access credentials (password)
     * If a valid token is passed actual change is perfomed, otherwise change is requested and token is
     * sent directly to user, tipically via email
     *
     * @return \Cake\Http\Response|null
     */
    public function change()
    {
        $this->request->allowMethod(['patch', 'post']);

        if ($this->request->is('post')) {
            $action = new ChangeCredentialsRequestAction();
            $action($this->request->getData());

            return $this->response
                ->withStatus(204);
        }

        $action = new ChangeCredentialsAction();
        $user = $action($this->request->getData());

        $meta = [];
        if ($this->request->getData('login')) {
            $userJwt = $this->reducedUserData($user->toArray());
            $meta = $this->jwtTokens($userJwt);
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
        $this->set('_meta', $meta);

        return null;
    }
}
