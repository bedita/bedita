<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018-2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use BEdita\API\Utility\JWTHandler;
use BEdita\Core\Model\Action\ActionTrait;
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Model\Entity\User;
use Cake\Auth\PasswordHasherFactory;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Controller for `/auth` endpoint.
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 * @property \BEdita\Core\Model\Table\AuthProvidersTable $AuthProviders
 */
class LoginController extends AppController
{
    use ActionTrait;

    /**
     * Default password hasher settings.
     *
     * @var array
     */
    public const PASSWORD_HASHER = [
        'className' => 'Fallback',
        'hashers' => [
            'Default',
            'Weak' => ['hashType' => 'md5'],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel('Users');
        $this->loadModel('AuthProviders');

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('parseJson', false);
        }

        if (in_array($this->request->getParam('action'), ['login', 'optout'])) {
            // $authenticationComponents = [
            //     AuthComponent::ALL => [
            //       'finder' => 'loginRoles',
            //     ],
            //     'Form' => [
            //         'fields' => [
            //             'username' => 'username',
            //             'password' => 'password_hash',
            //         ],
            //         'passwordHasher' => self::PASSWORD_HASHER,
            //     ],
            //     'BEdita/API.Jwt',
            // ];

            // $authenticationComponents += $this->AuthProviders
            //     ->find('authenticate')
            //     ->toArray();

            // $this->Auth->setConfig('authenticate', $authenticationComponents, false);
        }

        $this->Authentication->allowUnauthenticated(['login']);

        if ($this->request->getParam('action') === 'optout') {
            // $this->Auth->setConfig('loginAction', ['_name' => 'api:login:optout']);
        }

        if ($this->request->getParam('action') === 'change') {
            $this->request = $this->request->withAttribute('EndpointDefaultAuthorized', true);
        }
    }

    /**
     * Is identity required?
     *
     * @return bool
     */
    protected function isIdentityRequired(): bool
    {
        if ($this->request->getParam('action') === 'change') {
            return false;
        } elseif ($this->request->getParam('action') === 'whoami') {
            return true;
        }

        return parent::isIdentityRequired();
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
        $this->setSerialize([]);

        $this->setGrantType();
        $this->checkClientCredentials();

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
     * Try to setup appropriate grant type if missing looking at request data.
     * `grant_type` should be always set explicitly in request data.
     *
     * @return void
     */
    protected function setGrantType(): void
    {
        // if (!empty($this->request->getData('grant_type'))) {
        //     return;
        // }

        // $data = $this->request->getData();
        // if (empty($data)) {
        //     $this->request = $this->request->withData('grant_type', 'refresh_token');
        // } elseif (!empty($data['username']) && !empty($data['password'])) {
        //     $this->request = $this->request->withData('grant_type', 'password');
        // } elseif (!empty($data['client_id'])) {
        //     $this->request = $this->request->withData('grant_type', 'client_credentials');
        // }
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
            $this->setSerialize([]);
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
     *  - via JWT on refresh token grant type
     *
     * @return array
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception if user credentials are invalid or access is unauthorized
     */
    protected function identify(): array
    {
        $this->request->allowMethod('post');

        if ($this->clientCredentialsOnly()) {
            return [];
        }

        // if ($this->request->getData('password')) {
        //     $this->request = $this->request
        //         ->withData('password_hash', $this->request->getData('password'))
        //         ->withData('password', null);
        // }

        // $result = $this->Auth->identify();
        // if (!$result || !is_array($result)) {
        //     throw new UnauthorizedException(__('Login request not successful'));
        // }
        // // Result is a user; check endpoint permission on `/auth`
        // if (empty($result['authorization_code']) && !$this->Auth->isAuthorized($result)) {
        //     throw new UnauthorizedException(__('Login not authorized'));
        // }

        // return $result;

        if (!$this->Authentication->getResult()->isValid()) {
            throw new UnauthorizedException(__('Login request not successful'));
        }

        $result = $this->Authentication->getIdentity()->getOriginalData();
        if (is_array($result)) {
            return $result;
        }

        return $result->toArray();
    }

    /**
     * Check if we are dealing with client credentials only.
     * In case of `client_credentials` grant type or `refresh_token` grant type
     * with only client credentials renew we avoid user identification and return
     * only application related tokens.
     *
     * @return bool
     */
    protected function clientCredentialsOnly(): bool
    {
        if (empty($this->Authentication->getIdentity())) {
            return false;
        }

        return $this->Authentication->getIdentity()->getOriginalData() instanceof Application;

        // $grant = $this->request->getData('grant_type');
        // if (
        //     $grant === 'client_credentials' ||
        //     ($grant === 'refresh_token' && $this->Auth->getConfig('renewClientCredentials') === true)
        // ) {
        //     return true;
        // }

        // return false;
    }

    /**
     * Verify client application credentials `client_id/client_secret`.
     * Upon success the matching application is set via `CurrentApplication` otherwise
     * an `UnauthorizedException` will be thrown.
     *
     * @return void
     * @throws \Cake\Http\Exception\UnauthorizedException
     */
    protected function checkClientCredentials(): void
    {
        // $grantType = $this->request->getData('grant_type');
        // if (empty($this->request->getData('client_id')) && $grantType !== 'client_credentials') {
        //     return;
        // }
        // /** @var \BEdita\Core\Model\Entity\Application|null $application */
        // $application = TableRegistry::getTableLocator()->get('Applications')
        //     ->find('credentials', [
        //         'client_id' => $this->request->getData('client_id'),
        //         'client_secret' => $this->request->getData('client_secret'),
        //     ])
        //     ->first();
        // if (empty($application)) {
        //     throw new UnauthorizedException(__('App authentication failed'));
        // }
        // CurrentApplication::setApplication($application);
    }

    /**
     * Return a reduced version of user data with only
     * `id`, `username` and for each role `id` and `name
     *
     * @param array $userInput Complete user data
     * @return array Reduced user data (can be empty in case of client credentials)
     */
    protected function reducedUserData(array $userInput)
    {
        $user = array_intersect_key($userInput, array_flip(['id', 'username']));
        $user['roles'] = array_map(
            function ($role) {
                return [
                    'id' => Hash::get((array)$role, 'id'),
                    'name' => Hash::get((array)$role, 'name'),
                ];
            },
            (array)Hash::get($userInput, 'roles')
        );

        return array_filter($user);
    }

    /**
     * Calculate JWT token for auth and renew operations
     *
     * @param array $user Minimal user data to encode in JWT
     * @return array JWT tokens requested
     */
    protected function jwtTokens(array $user)
    {
        return JWTHandler::tokens($user, Router::reverse($this->request, true));
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
        $this->setSerialize(['user']);
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
        $this->setSerialize(['entity']);
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
        // $userId = $this->Auth->user('id');
        // if (!$userId) {
        //     $this->Auth->getAuthenticate('BEdita/API.Jwt')->unauthenticated($this->request, $this->response);
        // }
        $userId = $this->Authentication->getIdentityData('id');
        $contain = $this->prepareInclude($this->request->getQuery('include'));
        $contain = array_unique(array_merge($contain, ['Roles']));
        $conditions = ['id' => $userId];

        /** @var \BEdita\Core\Model\Entity\User|null $user */
        $user = $this->Users
            ->find('login', compact('conditions', 'contain'))
            ->first();
        if (empty($user)) {
            throw new UnauthorizedException(__('Request not authorized'));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    protected function findAssociation(string $relationship, ?Table $table = null): Association
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
            $action = $this->createAction('ChangeCredentialsRequestAction');
            $action($this->request->getData());

            return $this->response
                ->withStatus(204);
        }

        $action = $this->createAction('ChangeCredentialsAction');
        $user = $action($this->request->getData());

        $meta = [];
        if ($this->request->getData('login')) {
            $userJwt = $this->reducedUserData($user->toArray());
            $meta = $this->jwtTokens($userJwt);
        }

        $this->set(compact('user'));
        $this->setSerialize(['user']);
        $this->set('_meta', $meta);

        return null;
    }
}
