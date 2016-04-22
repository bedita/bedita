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
     *
     * @codingStandardsIgnore
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'reference to system user', 'precision' => null, 'autoIncrement' => null],
        'auth_provider_id' => ['type' => 'integer', 'length' => 5, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'link to external auth provider: ', 'precision' => null, 'autoIncrement' => null],
        'params' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'external auth params, serialized JSON', 'precision' => null],
        'username' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => 'auth username on provider', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'auth_provider_id' => ['type' => 'unique', 'columns' => ['auth_provider_id', 'username'], 'length' => []],
            'external_auth_ibfk_1' => ['type' => 'foreign', 'columns' => ['auth_provider_id'], 'references' => ['auth_providers', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'external_auth_ibfk_2' => ['type' => 'foreign', 'columns' => ['user_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
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
            'user_id' => 1,
            'auth_provider_id' => 1,
            'params' => null,
            'username' => 'first_user',
        ],
    ];
}
