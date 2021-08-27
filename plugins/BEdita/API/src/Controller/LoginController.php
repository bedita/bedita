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
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Entity\User;
use Cake\Auth\PasswordHasherFactory;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Controller for `/auth` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 * @property \BEdita\Core\Model\Table\AuthProvidersTable $AuthProviders
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

        $this->loadModel('Users');
        $this->loadModel('AuthProviders');

        if ($this->request->contentType() === 'application/json') {
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }

        if (in_array($this->request->getParam('action'), ['login', 'optout'])) {
            $authenticationComponents = [
                AuthComponent::ALL => [
                  'finder' => 'loginRoles',
                ],
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password_hash',
                    ],
                    'passwordHasher' => self::PASSWORD_HASHER,
                ],
                'BEdita/API.Jwt' => [
                    'queryDatasource' => true,
                ],
            ];

            $authenticationComponents += $this->AuthProviders
                ->find('authenticate')
                ->toArray();

            $this->Auth->setConfig('authenticate', $authenticationComponents, false);
        }

        if ($this->request->getParam('action') === 'optout') {
            $this->Auth->setConfig('loginAction', ['_name' => 'api:login:optout']);
        }

        if ($this->request->getParam('action') === 'change') {
            $this->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);
        }
    }

    /**
     * Login action via user identification with classic username/password, OTP, Oauth2 or 2FA.
     * See `identify` method for more details.
     *
     * @return void
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception if user credentials are invalid or acces is not authorized
     */
    public function login(): void
    {
        $this->set('_serialize', []);
        $result = $this->identify();
        // Check if result contains only an authorization code (OTP & 2FA use cases)
        if (!empty($result['authorization_code']) && count($result) === 1) {
            $this->set('_meta', ['authorization_code' => $result['authorization_code']]);

            return;
        }
        $user = $this->reducedUserData($result);
        $meta = $this->jwtTokens($user);
        $this->set('_meta', $meta);
    }

    /**
     * Optout action
     *
     * 1. User should be identified like in `login` with classic username\password or OPT/2FA or Oauth2 flow
     * 2. User data are deleted or anonymized like calling `DELETE /users/{id}`
     * 3. An event `Auth.optout` is dispatched in order to (optionally) remove some user created data or trigger other actions
     *
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception if user credentials are invalid or acces is not authorized
     */
    public function optout(): ?Response
    {
        $result = $this->identify();
        // Check if result contains only an authorization code (OTP & 2FA use cases)
        if (!empty($result['authorization_code']) && count($result) === 1) {
            $meta = ['authorization_code' => $result['authorization_code']];
            $this->set('_serialize', []);
            $this->set('_meta', $meta);

            return null;
        }
        // Execute actual optout
        $action = new GetObjectAction(['table' => $this->Users]);
        $user = $action(['primaryKey' => $result['id']]);
        // setup special `_optout` property to allow self-removal
        $user->set('_optout', true);
        $this->Users->deleteOrFail($user);
        $this->dispatchEvent('Auth.optout', [$result]);

        return $this->response->withStatus(204);
    }

    /**
     * User identification, used by `login` and `optout` actions:
     *
     *  - classic username and password
     *  - only with username, first step of OTP login
     *  - with username, authorization code and secret token as OTP login or 2FA access
     *
     * @return array
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception if user credentials are invalid or access is unauthorized
     */
    protected function identify(): array
    {
        $this->request->allowMethod('post');

        if ($this->request->getData('password')) {
            $this->request = $this->request
                ->withData('password_hash', $this->request->getData('password'))
                ->withData('password', null);
        }

        $result = $this->Auth->identify();
        if (!$result || !is_array($result)) {
            throw new UnauthorizedException(__('Login request not successful'));
        }
            // Result is a user; check endpoint permission on `/auth`
        if (empty($result['authorization_code']) && !$this->Auth->isAuthorized($result)) {
            throw new UnauthorizedException(__('Login not authorized'));
        }

        return $result;
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
     * @throws \Cake\Http\Exception\BadRequestException On invalid input data
     */
    public function update()
    {
        $this->request->allowMethod('patch');

        $entity = $this->userEntity();
        $entity->setAccess(['username', 'password_hash'], false);
        if (!empty($entity->get('email'))) {
            $entity->setAccess('email', false);
        }

        $data = $this->request->getData();
        $this->checkPassword($entity, $data);

        $action = new SaveEntityAction(['table' => $this->Users]);
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
     * @throws \Cake\Http\Exception\BadRequestException Throws an exception if current password is not correct.
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
     * Read logged user entity including roles and other related objects via `include` query string.
     *
     * @return \BEdita\Core\Model\Entity\User Logged user entity
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception if user not logged or blocked/removed
     */
    protected function userEntity()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            $this->Auth->getAuthenticate('BEdita/API.Jwt')->unauthenticated($this->request, $this->response);
        }
        $contain = $this->prepareInclude($this->request->getQuery('include'));
        $contain = array_unique(array_merge($contain, ['Roles']));
        $conditions = ['id' => $userId];

        /** @var \BEdita\Core\Model\Entity\User $user */
        $user = $this->Users
            ->find('login', compact('conditions', 'contain'))
            ->first();
        if (empty($user)) {
            throw new UnauthorizedException(__('Request not authorized'));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    protected function findAssociation(string $relationship, Table $table = null): Association
    {
        $relationship = Inflector::underscore($relationship);
        $association = $this->Users->associations()->getByProperty($relationship);
        if (empty($association)) {
            throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
        }

        return $association;
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
