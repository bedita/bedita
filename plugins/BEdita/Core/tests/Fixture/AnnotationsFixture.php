<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * AnnotationsFixture
 *
 */
class AnnotationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'object_id' => 2,
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Best regards.',
            'user_id' => 1,
            'created' => '2018-02-17 10:23:15',
            'modified' => '2018-02-17 10:23:15',
            'params' => '"something"'
        ],
        [
            'object_id' => 3,
            'description' => 'Gustavo for President!',
            'user_id' => 5,
            'created' => '2018-06-17 13:34:25',
            'modified' => '2018-06-17 13:34:25',
            'params' => '1'
        ],
    ];
}
