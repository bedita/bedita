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
 * Fixture for `external_auth` table.
 */
class ExternalAuthFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $table = 'external_auth';

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'user_id' => 1,
            'auth_provider_id' => 1,
            'params' => null,
            'provider_username' => 'first_user',
        ],
    ];
}
