<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

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
            'params' => '',
        ],
        [
            'left_id' => 3,
            'relation_id' => 1,
            'right_id' => 4,
            'priority' => 1,
            'inv_priority' => 1,
            'params' => '',
        ],
        [
            'left_id' => 2,
            'relation_id' => 1,
            'right_id' => 3,
            'priority' => 2,
            'inv_priority' => 1,
            'params' => '',
        ],
        [
            'left_id' => 1,
            'relation_id' => 2,
            'right_id' => 8,
            'priority' => 1,
            'inv_priority' => 1,
            'params' => '',
        ],
    ];
}
