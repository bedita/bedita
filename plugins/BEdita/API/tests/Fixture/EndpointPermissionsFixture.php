<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Permissive endpoint permissions for tests.
 */
class EndpointPermissionsFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'endpoint_id' => null,
            'application_id' => null,
            'role_id' => null,
            'permission' => 0b1111,
        ],
    ];
}
