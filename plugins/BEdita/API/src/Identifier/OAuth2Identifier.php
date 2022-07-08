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
use BEdita\Core\Utility\OAuth2;
use Cake\Utility\Hash;

class OAuth2Identifier extends AbstractIdentifier
{
    use ResolverAwareTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
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
        /** @var \BEdita\Core\Model\Entity\AuthProvider $authProvider */
        $authProvider = $this->getConfig('authProvider');
        $options = (array)Hash::get((array)$authProvider->get('params'), 'options');
        $providerResponse = $this->getOAuth2Response(
            $authProvider->get('url'),
            $credentials['access_token'],
            $options
        );

        if (!$authProvider->checkAuthorization($providerResponse, $credentials['provider_username'])) {
            return false;
        }

        $externalAuth = [
            'auth_provider' => $authProvider,
            'username' => $credentials['provider_username'],
        ];

        /** var \Authentication\Identifier\Resolver\OrmResolver $resolver */
        $resolver = $this->getResolver();
        $resolver->setConfig('finder', compact('externalAuth'));

        return $resolver->find([]);
    }

    /**
     * Get response from an OAuth2 provider
     *
     * @param string $url OAuth2 provider URL
     * @param string $accessToken Access token to use in request
     * @param array $options OAuth2 request options
     * @return array Response from an OAuth2 provider
     * @codeCoverageIgnore
     */
    protected function getOAuth2Response(string $url, string $accessToken, array $options = []): array
    {
        return (new OAuth2())->response($url, $accessToken, $options);
    }
}
