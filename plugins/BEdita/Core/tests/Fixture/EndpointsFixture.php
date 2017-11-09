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
 * EndpointsFixture
 *
 * @since 4.0.0
 */
class EndpointsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'name' => 'auth',
            'description' => '/auth endpoint',
            'created' => '2016-11-07 13:32:25',
            'modified' => '2016-11-07 13:32:25',
            'enabled' => 1,
            'object_type_id' => null,
        ],
        [
            'name' => 'home',
            'description' => '/home endpoint',
            'created' => '2016-11-07 13:32:26',
            'modified' => '2016-11-07 13:32:26',
            'enabled' => 1,
            'object_type_id' => null,
        ],
        [
            'name' => 'disabled',
            'description' => '/disabled endpoint',
            'created' => '2017-05-03 07:12:26',
            'modified' => '2017-05-03 07:12:26',
            'enabled' => 0,
            'object_type_id' => null,
        ],
    ];
}
