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
namespace BEdita\API\Authenticator;

use Authentication\Authenticator\FormAuthenticator;

class OAuth2Authenticator extends FormAuthenticator
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'fields' => [
            'auth_provider' => 'auth_provider',
            'provider_username' => 'provider_username',
            'access_token' => 'access_token',
        ],
    ];
}
