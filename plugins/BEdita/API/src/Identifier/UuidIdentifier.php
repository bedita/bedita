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

use Authentication\Identifier\TokenIdentifier;
use Cake\ORM\Locator\LocatorAwareTrait;

class UuidIdentifier extends TokenIdentifier
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'tokenField' => 'token',
        'dataField' => self::CREDENTIAL_TOKEN,
        'resolver' => [
            'className' => 'Authentication.Orm',
            'userModel' => 'Users',
        ],
        'authProvider' => null,
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials)
    {
        $authProvider = $this->getConfig('auth_provider');
        $externalAuth = [
            'auth_provider' => $authProvider,
            'username' => $credentials[self::CREDENTIAL_TOKEN],
        ];

        /** var \Authentication\Identifier\Resolver\OrmResolver $resolver */
        $resolver = $this->getResolver();
        $resolver->setConfig('finder', compact('externalAuth'));

        $identity = $resolver->find([]);
        if (!empty($identity)) {
            return $identity;
        }

        $Table = $this->fetchTable($this->getConfig('resolver.userModel'));
        $providerUsername = $credentials[self::CREDENTIAL_TOKEN];
        $Table->dispatchEvent('Auth.externalAuth', compact('authProvider', 'providerUsername'));

        return $resolver->find([]);
    }
}
