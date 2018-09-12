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

namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `auth_providers` table.
 */
class AuthProvidersFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'name' => 'example',
            'auth_class' => 'BEdita/API.OAuth2',
            'url' => 'https://example.com/oauth2',
            'params' => '{"provider_username_field": "owner_id"}',
            'enabled' => true,
            'created' => '2018-04-07 12:51:27',
            'modified' => '2018-04-07 12:51:27',
        ],
        [
            'name' => 'uuid',
            'auth_class' => 'BEdita/API.Uuid',
            'url' => null,
            'params' => null,
            'enabled' => true,
            'created' => '2018-04-07 12:51:27',
            'modified' => '2018-04-07 12:51:27',
        ],
        [
            'name' => 'linkedout',
            'auth_class' => 'BEdita/API.OAuth2',
            'url' => 'https://out.example.com/oauth2',
            'params' => '{"provider_username_field": "owner_id"}',
            'enabled' => false,
            'created' => '2018-04-07 12:51:27',
            'modified' => '2018-04-07 12:51:27',
        ],
        [
            'name' => 'otp',
            'auth_class' => 'BEdita/API.OTP',
            'url' => null,
            'params' => '{"generator":"BEdita/API.ConstGenerator"}',
            'enabled' => true,
            'created' => '2018-04-07 12:51:27',
            'modified' => '2018-04-07 12:51:27',
        ],
    ];
}
