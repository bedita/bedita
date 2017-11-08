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
            'name' => 'another_title',
            'property_type_id' => 1,
            'object_type_id' => 2,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'another_description',
            'property_type_id' => 1,
            'object_type_id' => 2,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => 'Brief description',
            'list_view' => false,
        ],
        [
            'name' => 'another_username',
            'property_type_id' => 1,
            'object_type_id' => 4,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'Username, unique string',
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'another_email',
            'property_type_id' => 1,
            'object_type_id' => 4,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'User email',
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'another_birthdate',
            'property_type_id' => 2,
            'object_type_id' => 3,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => 'Date of birth',
            'list_view' => false,
        ],
        [
            'name' => 'another_surname',
            'property_type_id' => 1,
            'object_type_id' => 3,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'disabled_property',
            'property_type_id' => 1,
            'object_type_id' => 9,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2017-09-05 11:10:00',
            'modified' => '2017-09-05 11:10:00',
            'description' => 'Disabled property example',
            'enabled' => 0,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'media_property',
            'property_type_id' => 1,
            'object_type_id' => 8,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
        [
            'name' => 'files_property',
            'property_type_id' => 1,
            'object_type_id' => 9,
            'multiple' => 0,
            'options_list' => null,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => 1,
            'label' => null,
            'list_view' => true,
        ],
    ];
}
