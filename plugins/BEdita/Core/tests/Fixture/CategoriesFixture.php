<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
 * CategoriesFixture
 */
class CategoriesFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        // 1
        [
            'object_type_id' => 2,
            'name' => 'first-cat',
            'label' => 'First category',
            'parent_id' => null,
            'tree_left' => 1,
            'tree_right' => 2,
            'enabled' => 1,
            'created' => '2019-11-25 17:35:58',
            'modified' => '2019-11-25 17:35:58'
        ],
        // 2
        [
            'object_type_id' => 2,
            'name' => 'second-cat',
            'label' => 'Second category',
            'parent_id' => null,
            'tree_left' => 3,
            'tree_right' => 4,
            'enabled' => 1,
            'created' => '2019-11-25 17:35:58',
            'modified' => '2019-11-25 17:35:58'
        ],
        // 3
        [
            'object_type_id' => 2,
            'name' => 'disabled-cat',
            'label' => 'Disabled category',
            'parent_id' => null,
            'tree_left' => 5,
            'tree_right' => 6,
            'enabled' => 0,
            'created' => '2019-11-26 12:15:51',
            'modified' => '2019-11-26 12:15:51'
        ],
        // 4
        [
            'object_type_id' => null,
            'name' => 'first-tag',
            'label' => 'First tag',
            'parent_id' => null,
            'tree_left' => null,
            'tree_right' => null,
            'enabled' => 1,
            'created' => '2019-11-26 12:15:51',
            'modified' => '2019-11-26 12:15:51'
        ],
    ];
}
