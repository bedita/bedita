<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
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
 * Fixture for `object_relations` table.
 */
class ObjectRelationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'left_id' => 2,
            'relation_id' => 1,
            'right_id' => 4,
            'priority' => 1,
            'inv_priority' => 2,
            'params' => null,
        ],
        [
            'left_id' => 3,
            'relation_id' => 1,
            'right_id' => 4,
            'priority' => 1,
            'inv_priority' => 1,
            'params' => null,
        ],
        [
            'left_id' => 2,
            'relation_id' => 1,
            'right_id' => 3,
            'priority' => 2,
            'inv_priority' => 1,
            'params' => null,
        ],
        [
            'left_id' => 1,
            'relation_id' => 2,
            'right_id' => 8,
            'priority' => 1,
            'inv_priority' => 1,
            'params' => null,
        ],
    ];
}
