<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * RelationTypesFixture
 *
 */
class RelationTypesFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'relation_id' => 1,
            'object_type_id' => 1,
            'side' => 'left'
        ],
    ];

    /**
     * Before Build Schema callback
     *
     * Change `side` type to 'string' to avoid errors
     *
     * @return void
     */
    public function beforeBuildSchema()
    {
        $this->fields['side']['type'] = 'string';
    }
}
