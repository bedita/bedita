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
 * Fixture for `relations` table.
 */
class RelationsFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'name' => 'test',
            'label' => 'Test relation',
            'inverse_name' => 'inverse_test',
            'inverse_label' => 'Inverse test relation',
            'description' => 'Sample description.',
            'params' => null,
        ],
        [
            'name' => 'another_test',
            'label' => 'Another test relation',
            'inverse_name' => 'inverse_another_test',
            'inverse_label' => 'Another inverse test relation',
            'description' => 'Sample description /2.',
            'params' => '{"type":"object","properties":{"name":{"type":"string"},"age":{"type":"integer","minimum":0}},"required":["name"]}',
        ],
    ];
}
