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
 * Fixture for `config` table.
 */
class ConfigFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     */
    public $table = 'config';

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'name' => 'Name1',
            'context' => 'group1',
            'content' => 'data',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'Name2',
            'context' => 'group1',
            'content' => 'true',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'Key2',
            'context' => 'group1',
            'content' => '{"test1" : "some data", "test2" : "other data"}',
            'created' => '2016-06-14 12:34:56',
            'modified' => '2016-06-15 12:38:02',
        ],
        [
            'name' => 'IntVal',
            'context' => 'group2',
            'content' => 14,
            'created' => '2016-06-13 17:34:56',
            'modified' => '2016-06-14 16:38:02',
        ],
        [
            'name' => 'lowercaseGroup.trueVal',
            'context' => 'lowercaseGroup',
            'content' => 'true',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'lowercaseGroup.falseVal',
            'context' => 'lowercaseGroup',
            'content' => 'false',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'lowercaseGroup.nullVal',
            'context' => 'lowercaseGroup',
            'content' => 'null',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'uppercaseGroup.trueVal',
            'context' => 'uppercaseGroup',
            'content' => 'TRUE',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'uppercaseGroup.falseVal',
            'context' => 'uppercaseGroup',
            'content' => 'FALSE',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'uppercaseGroup.nullVal',
            'context' => 'uppercaseGroup',
            'content' => 'NULL',
            'created' => '2016-06-16 12:34:56',
            'modified' => '2016-06-16 12:38:02',
        ],
        [
            'name' => 'appVal',
            'context' => 'core',
            'content' => '{"val": 42}',
            'created' => '2018-05-16 12:34:56',
            'modified' => '2018-05-16 12:38:02',
            'application_id' => 1,
        ],
        [
            'name' => 'someVal',
            'context' => 'somecontext',
            'content' => 42,
            'created' => '2018-05-16 12:34:56',
            'modified' => '2018-05-16 12:38:02',
            'application_id' => 1,
        ]
    ];
}
