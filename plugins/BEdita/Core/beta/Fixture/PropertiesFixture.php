<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * PropertiesFixture
 *
 */
class PropertiesFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'name' => 'title',
            'property_type_id' => 1,
            'object_type_id' => 1,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true
        ],
        [
            'name' => 'description',
            'property_type_id' => 1,
            'object_type_id' => 1,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => 'Brief description',
            'list_view' => false
        ],
        [
            'name' => 'username',
            'property_type_id' => 1,
            'object_type_id' => 3,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'Username, unique string',
            'enabled' => 1,
            'label' => null,
            'list_view' => true
        ],
        [
            'name' => 'email',
            'property_type_id' => 1,
            'object_type_id' => 3,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'User email',
            'enabled' => 1,
            'label' => null,
            'list_view' => true
        ],
        [
            'name' => 'birthdate',
            'property_type_id' => 2,
            'object_type_id' => 2,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => 'Date of birth',
            'list_view' => false
        ],
        [
            'name' => 'surname',
            'property_type_id' => 1,
            'object_type_id' => 2,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true
        ],
    ];
}
