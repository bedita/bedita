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

use BEdita\Core\Model\Entity\AuthProvider;
use BEdita\Core\State\CurrentApplication;
use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Utility\Text;

/**
 * Authenticate users via One Time Password.
 *
 * @since 4.0.0
 */
class OTPAuthenticate extends BaseAuthenticate
{
    use EventDispatcherTrait;

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
     *  - `passwordHasher` Password hasher class. Can be a string specifying class name
     *    or an array containing `className` key, any other keys will be passed as
     *    config to the class. Defaults to 'Default'.
     * - 'expiry' Expiry time of a user token, expressed as string expression like `+1 hour`, `+10 minutes`
     * - 'generator' Secret token generator, if a valid callable is used instead of default one.
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
        'finder' => 'loginRoles',
        'passwordHasher' => 'Default',
        'expiry' => '+15 minutes',
        'generator' => null,
    ];

    /**
     * @inheritDoc
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        // override configuration with `otp` auth provider params ('generator', 'expiry'...)
        $authProvider = $this->getConfig('authProviders.otp');
        if ($authProvider && $authProvider instanceof AuthProvider) {
            $this->setConfig((array)$authProvider->get('params'), true);
        }
    }

    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $username = $request->getData('username');
        if (empty($username) || !is_string($username)) {
            return false;
        }

        $grant = $request->getData('grant_type');
        if ($grant === 'otp') {
            return $this->otpAccess($username, $request);
        } elseif ($grant === 'otp_request') {
            return $this->otpRequest($username);
        }

        return false;
    }

    /**
     * Retrieve access grant using authorization code and secret token.
     *
     * @param string $username User name
     * @param \Cake\Http\ServerRequest $request Request object
     * @return array|bool User data array on success, false on failure
     */
    protected function otpAccess($username, ServerRequest $request)
    {
        if (empty($request->getData('authorization_code')) || empty($request->getData('token'))) {
            return false;
        }

        $result = $this->_findUser($username);
        if (empty($result)) {
            return false;
        }

        $data = [
            'user_id' => $result['id'],
            'application_id' => CurrentApplication::getApplicationId(),
            'client_token' => $request->getData('authorization_code'),
            'secret_token' => $request->getData('token'),
            'token_type' => 'otp',
        ];

        $UserTokens = TableRegistry::getTableLocator()->get('UserTokens');
        $userToken = $UserTokens->find('valid')->where($data)->first();
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
     * @return array|bool Authorization code array on success, false on failure
     */
    protected function otpRequest($username)
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
            'expires' => new FrozenTime($this->getConfig('expiry')),
        ];

        $UserTokens = TableRegistry::getTableLocator()->get('UserTokens');
        $entity = $UserTokens->newEntity($data);
        $UserTokens->saveOrFail($entity);
        $this->dispatchEvent('Auth.userToken', [$entity]);

        return ['authorization_code' => $data['client_token']];
    }

    /**
     * Generate authorization code, aka client token.
     *
     * @return string The generated token
     * @codeCoverageIgnore
     */
    public function generateClientToken()
    {
        return Text::uuid();
    }

    /**
     * Generate secret token, to be sent separately in a secure way to user
     *
     * @return string The generated secure token
     */
    public function generateSecretToken()
    {
        $generator = $this->getConfig('generator');
        if (!empty($generator) && is_callable($generator)) {
            return call_user_func($generator);
        }

        return $this->defaultSecretGenerator();
    }

    /**
     * Super-simple default secret generator: string of 6 random digits
     *
     * @return string The generated secure token
     */
    public static function defaultSecretGenerator()
    {
        return sprintf('%06d', hexdec(bin2hex(Security::randomBytes(2))));
    }
}
