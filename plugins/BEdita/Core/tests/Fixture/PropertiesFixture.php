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
 * PropertiesFixture
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
            'is_static' => false,
            'read_only' => false,
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
            'is_static' => false,
            'read_only' => false,
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
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'another_email',
            'property_type_id' => 4,
            'object_type_id' => 4,
            'created' => '2016-12-31 23:09:23',
            'modified' => '2016-12-31 23:09:23',
            'description' => 'User email',
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => false,
            'read_only' => false,
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
            'is_static' => false,
            'read_only' => true,
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
            'is_static' => false,
            'read_only' => false,
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
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'media_property',
            'property_type_id' => 10,
            'object_type_id' => 8,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => false,
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'files_property',
            'property_type_id' => 11,
            'object_type_id' => 9,
            'created' => '2017-11-07 18:32:00',
            'modified' => '2017-11-07 18:32:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'street_address',
            'property_type_id' => 1,
            'object_type_id' => 3,
            'created' => '2020-08-07 16:23:00',
            'modified' => '2020-08-07 16:23:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => true,
            'read_only' => false,
        ],
        [
            'name' => 'number_of_friends',
            'property_type_id' => 9,
            'object_type_id' => 3,
            'created' => '2021-07-13 18:30:00',
            'modified' => '2021-07-13 18:30:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'children_order',
            'property_type_id' => 13,
            'object_type_id' => 10,
            'created' => '2022-12-01 15:26:00',
            'modified' => '2022-12-01 15:26:00',
            'description' => null,
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => false,
            'read_only' => false,
        ],
        [
            'name' => 'default_val_property',
            'property_type_id' => 1, // string
            'object_type_id' => 9, // documents
            'created' => '2024-07-04 11:25:58',
            'modified' => '2024-07-04 11:25:58',
            'description' => 'Default val property example',
            'enabled' => true,
            'label' => null,
            'is_nullable' => true,
            'is_static' => false,
            'read_only' => false,
            'default_value' => 'my default value',
        ],
    ];
}
