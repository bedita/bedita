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
            'priority' => 1,
            'depth_level' => 0,
            'menu' => 0,
            'canonical' => 0,
            'slug' => 'root-folder-11',
        ],

        // sub folder
        [
            'object_id' => 12,
            'parent_id' => 11,
            'root_id' => 11,
            'parent_node_id' => 1,
            'priority' => 1,
            'depth_level' => 1,
            'menu' => 1,
            'canonical' => 1,
            'slug' => 'sub-folder-12',
        ],

        // document in root folder
        [
            'object_id' => 2,
            'parent_id' => 11,
            'root_id' => 11,
            'parent_node_id' => 1,
            'priority' => 2,
            'depth_level' => 1,
            'menu' => 1,
            'canonical' => 1,
            'slug' => 'title-one-2',
        ],

        // profile in sub folder
        [
            'object_id' => 4,
            'parent_id' => 12,
            'root_id' => 11,
            'parent_node_id' => 2,
            'priority' => 1,
            'depth_level' => 2,
            'menu' => 1,
            'canonical' => 1,
            'slug' => 'gustavo-supporto-profile-4',
        ],

        // another root folder
        [
            'object_id' => 13,
            'parent_id' => null,
            'root_id' => 13,
            'parent_node_id' => null,
            'priority' => 2,
            'depth_level' => 0,
            'menu' => 1,
            'canonical' => 0,
            'slug' => 'another-root-folder-13',
        ],
    ];
}
