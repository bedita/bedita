<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * TreesFixture
 *
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
            'menu' => 0
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
            'menu' => 1
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
            'menu' => 1
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
            'menu' => 1
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
            'menu' => 1
        ],
    ];
}
