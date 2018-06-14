<?php

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `relation_types` table.
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
            'relation_id' => 1, // test / inverse_test
            'object_type_id' => 2, // documents
            'side' => 'left',
        ],
        [
            'relation_id' => 1, // test / inverse_test
            'object_type_id' => 2, // documents
            'side' => 'right',
        ],
        [
            'relation_id' => 1, // test / inverse_test
            'object_type_id' => 3, // profiles
            'side' => 'right',
        ],
        [
            'relation_id' => 2, // another_test / inverse_another_test
            'object_type_id' => 6, // locations
            'side' => 'left',
        ],
        [
            'relation_id' => 2, // another_test / inverse_another_test
            'object_type_id' => 6, // locations
            'side' => 'right',
        ],
        [
            'relation_id' => 3, // test_abstract / inverse_test_abstract
            'object_type_id' => 9, // files
            'side' => 'left',
        ],
        [
            'relation_id' => 3, // test_abstract / inverse_test_abstract
            'object_type_id' => 8, // media
            'side' => 'right',
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
