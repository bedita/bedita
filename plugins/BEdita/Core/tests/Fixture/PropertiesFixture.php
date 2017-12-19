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
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'another_description',
            'property_type_id' => 1,
            'object_type_id' => 2,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => true,
            'label' => 'Brief description',
            'is_nullable' => true,
        ],
        [
            'name' => 'another_username',
            'property_type_id' => 1,
            'object_type_id' => 4,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'Username, unique string',
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'another_email',
            'property_type_id' => 1,
            'object_type_id' => 4,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'User email',
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'another_birthdate',
            'property_type_id' => 6,
            'object_type_id' => 3,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => true,
            'label' => 'Date of birth',
            'is_nullable' => true,
        ],
        [
            'name' => 'another_surname',
            'property_type_id' => 1,
            'object_type_id' => 3,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'disabled_property',
            'property_type_id' => 1,
            'object_type_id' => 9,
            'created' => '2017-09-05 11:10:00',
            'modified' => '2017-09-05 11:10:00',
            'description' => 'Disabled property example',
            'enabled' => false,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'media_property',
            'property_type_id' => 1,
            'object_type_id' => 8,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
        [
            'name' => 'files_property',
            'property_type_id' => 1,
            'object_type_id' => 9,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
        ],
    ];
}
