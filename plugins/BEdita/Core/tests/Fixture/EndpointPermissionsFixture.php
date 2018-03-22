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
 * EndpointPermissionsFixture
 *
 * @since 4.0.0
 */
class EndpointPermissionsFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'endpoint_id' => null,
            'application_id' => null,
            'role_id' => null,
            'permission' => 0,
        ],
        [
            'endpoint_id' => null,
            'application_id' => 1,
            'role_id' => null,
            'permission' => 0b1111,
        ],
        [
            'endpoint_id' => 2,
            'application_id' => 2,
            'role_id' => 1,
            'permission' => 0b1001,
        ],
        [
            'endpoint_id' => 2,
            'application_id' => 2,
            'role_id' => 2,
            'permission' => 0,
        ],
        [
            'endpoint_id' => 1,
            'application_id' => 2,
            'role_id' => 2,
            'permission' => 0b0001,
        ],
    ];
}
