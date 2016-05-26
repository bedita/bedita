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

class FakeArticlesFixture extends TestFixture
{

    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => true],
        'title' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'precision' => null],
        'body' => ['type' => 'text'],
        'fake_animal_id' => ['type' => 'integer', 'null' => true],
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
        ['title' => 'The cat', 'body' => 'article body', 'fake_animal_id' => 1],
        ['title' => 'Puss in boots', 'body' => 'text', 'fake_animal_id' => 1]
    ];
}
