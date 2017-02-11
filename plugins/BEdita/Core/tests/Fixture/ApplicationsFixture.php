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
 * ApplicationsFixture
 *
 * @since 4.0.0
 */
class ApplicationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'api_key' => 'API_KEY',
            'name' => 'First app',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
            'created' => '2016-10-28 07:10:57',
            'modified' => '2016-10-28 07:10:57',
            'enabled' => 1,
        ],
    ];
}
