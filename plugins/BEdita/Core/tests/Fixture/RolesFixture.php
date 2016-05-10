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
 * Fixture for `roles` table.
 */
class RolesFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->records = [
            [
                'name' => 'first role',
                'description' => 'this is the very first role',
                'immutable' => 1,
                'backend_auth' => 1,
                'created' => '2016-04-15 09:57:38',
                'modified' => '2016-04-15 09:57:38',
            ],
            [
                'name' => 'second role',
                'description' => 'this is a second role',
                'immutable' => 0,
                'backend_auth' => 0,
                'created' => '2016-04-15 11:59:12',
                'modified' => '2016-04-15 11:59:13',
            ],
        ];

        parent::init();
    }
}
