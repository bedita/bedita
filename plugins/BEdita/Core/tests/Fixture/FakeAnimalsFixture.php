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

class FakeAnimalsFixture extends TestFixture
{

    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => true],
        'name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null],
        'legs' => ['type' => 'integer', 'length' => 2, 'null' => true, 'default' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public $records = [
        ['name' => 'cat', 'legs' => 4],
        ['name' => 'koala', 'legs' => 4],
        ['name' => 'eagle', 'legs' => 2],
    ];
}
