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
 * Fixture for `fake_felines` table.
 */
class FakeFelinesFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'family' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fakefelines_fk' => [
                'type' => 'foreign',
                'columns' => ['id'],
                'references' => ['fake_mammals', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction'
            ]
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
        ['family' => 'purring cats'],
    ];
}
