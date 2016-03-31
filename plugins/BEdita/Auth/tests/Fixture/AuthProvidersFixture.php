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

namespace BEdita\Auth\Test\Fixture;

use BEdita\Auth\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `auth_providers` table.
 */
class AuthProvidersFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     *
     * @codingStandardsIgnore
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 5, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => 'external provider name: facebook, google, github...', 'precision' => null, 'fixed' => null],
        'url' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => 'external provider url', 'precision' => null, 'fixed' => null],
        'params' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'external provider parameters', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'name' => ['type' => 'unique', 'columns' => ['name'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'name' => 'example',
            'url' => 'https://example.com/oauth2',
            'params' => '{}',
        ],
        [
            'name' => 'example_2',
            'url' => 'https://example.org/oauth2',
            'params' => '{"param":"value"}',
        ],
    ];
}
