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

namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\ChangeCredentialsAction;
use BEdita\Core\Model\Action\ChangeCredentialsRequestAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Entity\User;
use Cake\Auth\PasswordHasherFactory;
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
     * Default password hasher settings.
     *
     * @var array
     */
    const PASSWORD_HASHER = [
        'className' => 'Fallback',
        'hashers' => [
            'Default',
            'Weak' => ['hashType' => 'md5'],
        ],
    ];

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
                    'passwordHasher' => self::PASSWORD_HASHER,
                    'finder' => 'login',
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
     * Login action use cases:
     *
     *  - classic username and password
     *  - only with username, first step of OTP login
     *  - with username, authorization code and secret token as OTP login or 2FA access
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

        $result = $this->Auth->identify();
        if (!$result) {
            throw new UnauthorizedException(__('Login request not successful'));
        }

        // Check if result contains only an authorization code (OTP & 2FA use cases)
        if (!empty($result['authorization_code']) && count($result) === 1) {
            $meta = ['authorization_code' => $result['authorization_code']];
        } else {
            // Result is a user; check endpoint permission on `/auth`
            if (!$this->Auth->isAuthorized($result)) {
                throw new UnauthorizedException(__('Login not authorized'));
            }
            $result = $this->reducedUserData($result);
            $meta = $this->jwtTokens($result);
        }

        $this->set('_serialize', []);
        $this->set('_meta', $meta);
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
            Security::getSalt(),
            $algorithm
        );
        $renew = JWT::encode(
            $claims + ['sub' => $user['id'], 'aud' => $currentUrl],
            Security::getSalt(),
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

        $this->set('_fields', $this->request->getQuery('fields', []));
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
        $entity->setAccess(['username', 'password_hash', 'email'], false);

        $data = $this->request->getData();
        $this->checkPassword($entity, $data);

        $action = new SaveEntityAction(['table' => TableRegistry::get('Users')]);
        $action(compact('entity', 'data'));

        // reload entity to cancel previous `setAccess` (otherwise `username` and `email` will appear in `meta`)
        $entity = $this->userEntity();
        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);
    }

    /**
     * Check current password if a password change is requested.
     * If `password` is set in requesta data current valid password must be in `old_password`
     *
     * @param \BEdita\Core\Model\Entity\User $entity Logged user entity.
     * @param array $data Request data.
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if current password is not correct.
     * @return void
     */
    protected function checkPassword(User $entity, array $data)
    {
        if (empty($data['password'])) {
            return;
        }

        if (empty($data['old_password'])) {
            throw new BadRequestException(__d('bedita', 'Missing current password'));
        }

        $hasher = PasswordHasherFactory::build(self::PASSWORD_HASHER);
        if (!$hasher->check($data['old_password'], $entity->password_hash)) {
            throw new BadRequestException(__d('bedita', 'Wrong password'));
        }
    }

    /**
     * Read logged user entity including roles.
     *
     * @return \BEdita\Core\Model\Entity\User Logged user entity
     * @throws \Cake\Network\Exception\UnauthorizedException Throws an exception if user not logged.
     */
    protected function userEntity()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            $this->Auth->getAuthenticate('BEdita/API.Jwt')->unauthenticated($this->request, $this->response);
        }

        return TableRegistry::get('Users')->get($userId, ['contain' => ['Roles']]);
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
