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
            'params' => '{"type":"string"}',
        ],
        [
            'name' => 'status',
            'params' => '{"type":"string","enum":["on","off","draft"]}',
        ],
        [
            'name' => 'email',
            'params' => '{"type":"string","format":"email"}',
        ],
        [
            'name' => 'url',
            'params' => '{"type":"string","format":"uri"}',
        ],
        [
            'name' => 'date',
            'params' => '{"type":"string","format":"date-time"}',
        ],
        [
            'name' => 'number',
            'params' => '{"type":"number"}',
        ],
        [
            'name' => 'boolean',
            'params' => '{"type":"boolean"}',
        ],
        [
            'name' => 'json',
            'params' => '{"type":"object"}',
        ],
        [
            'name' => 'unused property type',
            'params' => '{"type":"object","properties":{"gustavo":{"const":"supporto"}},"required":["gustavo"]}',
        ],
    ];
}
