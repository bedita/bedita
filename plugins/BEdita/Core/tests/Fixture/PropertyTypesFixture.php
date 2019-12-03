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
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'text',
            'params' => '{"type":"string","contentMediaType":"text/html"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'status',
            'params' => '{"type":"string","enum":["on","off","draft"]}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'email',
            'params' => '{"type":"string","format":"email"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'url',
            'params' => '{"type":"string","format":"uri"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'date',
            'params' => '{"type":"string","format":"date"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'datetime',
            'params' => '{"type":"string","format":"date-time"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'number',
            'params' => '{"type":"number"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'integer',
            'params' => '{"type":"integer"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'boolean',
            'params' => '{"type":"boolean"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'json',
            'params' => '{"type":"object"}',
            'created' => '2019-11-01 09:23:43',
            'modified' => '2019-11-01 09:23:43',
            'core_type' => true,
        ],
        [
            'name' => 'unused property type',
            'params' => '{"type":"object","properties":{"gustavo":{"const":"supporto"}},"required":["gustavo"]}',
            'created' => '2019-11-02 09:23:43',
            'modified' => '2019-11-02 09:23:43',
            'core_type' => false,
        ],
    ];
}
