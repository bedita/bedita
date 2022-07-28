<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Identifier;

use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\Resolver\ResolverAwareTrait;
use Cake\Event\EventDispatcherTrait;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Utility\Text;

/**
 * @property \BEdita\Core\Model\Table\UserTokensTable $UserTokens
 */
class OTPIdentifier extends AbstractIdentifier
{
    use EventDispatcherTrait;
    use ResolverAwareTrait;
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'resolver' => [
            'className' => 'Authentication.Orm',
            'userModel' => 'Users',
            'finder' => 'loginRoles',
        ],
        'authProvider' => null,
        'passwordHasher' => 'Default',
        'expiry' => '+15 minutes',
        'generator' => null,
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials)
    {
        $username = (string)Hash::get($credentials, 'username');
        if (empty($username)) {
            return null;
        }

        $this->UserTokens = $this->fetchTable('UserTokens');

        $otp = (string)Hash::get($credentials, 'otp');
        if ($otp === 'request') {
            return $this->otpRequest($username);
        } elseif ($otp === 'access') {
            $authCode = (string)Hash::get($credentials, 'authorization_code');
            $token = (string)Hash::get($credentials, 'token');

            return $this->otpAccess($username, $authCode, $token);
        }

        return null;
    }

    /**
     * Retrieve access grant using authorization code and secret token.
     *
     * @param string $username User name
     * @param string $authCode Authorization code
     * @param string $token The secret token
     * @return \ArrayAccess|array|null User data array on success, null on failure
     */
    protected function otpAccess(string $username, string $authCode, string $token)
    {
        $result = $this->getResolver()->find(compact('username'));
        if (empty($result)) {
            return null;
        }

        $data = [
            'user_id' => $result['id'],
            // FIXME: current application ID not available now...
            // 'application_id' => CurrentApplication::getApplicationId(),
            'client_token' => $authCode,
            'secret_token' => $token,
            'token_type' => 'otp',
        ];

        $userToken = $this->UserTokens->find('valid')->where($data)->first();
        if (!empty($userToken)) {
            $this->UserTokens->deleteOrFail($userToken);

            return $result;
        }

        return null;
    }

    /**
     * Generate a new client and secret token upon `otp_request`
     *
     * @param string $username User name
     * @return \ArrayAccess|array|null Authorization array on success, null on failure
     */
    protected function otpRequest($username)
    {
        $result = $this->getResolver()->find(compact('username'));
        if (empty($result)) {
            return null;
        }

        $data = [
            'user_id' => $result['id'],
            // FIXME: current application ID not available now...
            // 'application_id' => CurrentApplication::getApplicationId(),
            'client_token' => $this->generateClientToken(),
            'secret_token' => $this->generateSecretToken(),
            'token_type' => 'otp',
            'expires' => new FrozenTime($this->getConfig('expiry')),
        ];

        $entity = $this->UserTokens->newEntity($data);
        $this->UserTokens->saveOrFail($entity);
        $this->dispatchEvent('Auth.userToken', [$entity]);
        // $entity->unset(['user_id', 'secret_token', 'expires']);
        // $entity->set('authorization_code', $entity->get('client_token'));

        return ['authorization_code' => $entity->get('client_token')];
    }

    /**
     * Generate authorization code, aka client token.
     *
     * @return string The generated token
     * @codeCoverageIgnore
     */
    public function generateClientToken(): string
    {
        return Text::uuid();
    }

    /**
     * Generate secret token, to be sent separately in a secure way to user
     *
     * @return string The generated secure token
     */
    public function generateSecretToken(): string
    {
        $generator = $this->getConfig('generator');
        if (!empty($generator) && is_callable($generator)) {
            return (string)call_user_func($generator);
        }

        return $this->defaultSecretGenerator();
    }

    /**
     * Super-simple default secret generator: string of 6 random digits
     *
     * @return string The generated secure token
     */
    public static function defaultSecretGenerator(): string
    {
        return sprintf('%06d', hexdec(bin2hex(Security::randomBytes(2))));
    }
}
