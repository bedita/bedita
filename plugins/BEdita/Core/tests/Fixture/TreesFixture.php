<?php
declare(strict_types=1);

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
 * TreesFixture
 */
class TreesFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [

        // root folder
        [
            'object_id' => 11,
            'parent_id' => null,
            'root_id' => 11,
            'parent_node_id' => null,
            'tree_left' => 1,
            'tree_right' => 8,
            'depth_level' => 0,
            'menu' => 0,
            'canonical' => 0,
        ],

        // sub folder
        [
            'object_id' => 12,
            'parent_id' => 11,
            'root_id' => 11,
            'parent_node_id' => 1,
            'tree_left' => 2,
            'tree_right' => 5,
            'depth_level' => 1,
            'menu' => 1,
            'canonical' => 1,
        ],

        // document in root folder
        [
            'object_id' => 2,
            'parent_id' => 11,
            'root_id' => 11,
            'parent_node_id' => 1,
            'tree_left' => 6,
            'tree_right' => 7,
            'depth_level' => 1,
            'menu' => 1,
            'canonical' => 1,
        ],

        // profile in sub folder
        [
            'object_id' => 4,
            'parent_id' => 12,
            'root_id' => 11,
            'parent_node_id' => 2,
            'tree_left' => 3,
            'tree_right' => 4,
            'depth_level' => 2,
            'menu' => 1,
            'canonical' => 1,
        ],

        // another root folder
        [
            'object_id' => 13,
            'parent_id' => null,
            'root_id' => 13,
            'parent_node_id' => null,
            'tree_left' => 9,
            'tree_right' => 10,
            'depth_level' => 0,
            'menu' => 1,
            'canonical' => 0,
        ],
    ];
}
