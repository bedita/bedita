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
use Cake\Utility\Hash;

/**
 * Application identifier.
 */
class ApplicationIdentifier extends AbstractIdentifier
{
    use ResolverAwareTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'fields' => [
            self::CREDENTIAL_USERNAME => 'client_id',
            self::CREDENTIAL_PASSWORD => 'client_secret',
        ],
        'resolver' => [
            'className' => 'Authentication.Orm',
            'userModel' => 'Applications',
            'finder' => 'enabled',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials)
    {
        $credentials = [
            $this->getConfig('fields.' . self::CREDENTIAL_USERNAME) => Hash::get($credentials, self::CREDENTIAL_USERNAME),
            $this->getConfig('fields.' . self::CREDENTIAL_PASSWORD) => Hash::get($credentials, self::CREDENTIAL_PASSWORD),
        ];

        /** @var \Authentication\Identifier\Resolver\OrmResolver $resolver */
        $resolver = $this->getResolver();
        $resolver->setConfig('finder', compact('credentials'));

        return $resolver->find([]);
    }
}
