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

namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `fake_labels` table.
 */
class FakeLabelsFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => true],
        'fake_tag_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'color' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fakelabels_tagid_fk' => [
                'type' => 'foreign',
                'columns' => ['fake_tag_id'],
                'references' => ['fake_tags', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
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
        ['color' => 'red'],
        ['color' => 'green', 'fake_tag_id' => 1],
        ['color' => 'brown'],
    ];
}
