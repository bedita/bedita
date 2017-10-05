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
            'relation_id' => 1,
            'object_type_id' => 2,
            'side' => 'left',
        ],
        [
            'relation_id' => 1,
            'object_type_id' => 2,
            'side' => 'right',
        ],
        [
            'relation_id' => 1,
            'object_type_id' => 3,
            'side' => 'right',
        ],
        [
            'relation_id' => 2,
            'object_type_id' => 6,
            'side' => 'left',
        ],
        [
            'relation_id' => 2,
            'object_type_id' => 6,
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
