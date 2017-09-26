<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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
 * Fixture for `property_types` table.
 */
class PropertyTypesFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     */
    public $table = 'property_types';

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'name' => 'string',
            'params' => '{"type": "string"}'
        ],
        [
            'name' => 'date',
            'params' => '{"type": "string"}'
        ],
        [
            'name' => 'number',
            'params' => '{"type": "number"}'
        ],
        [
            'name' => 'boolean',
            'params' => '{"type": "boolean"}'
        ],
    ];
}
