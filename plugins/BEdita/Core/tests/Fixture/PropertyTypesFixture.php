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
     * @inheritDoc
     */
    public $table = 'property_types';

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $this->records = [
            // 1
            [
                'name' => 'string',
                'params' => ['type' => 'string'],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 2
            [
                'name' => 'text',
                'params' => [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 3
            [
                'name' => 'status',
                'params' => [
                    'type' => 'string',
                    'enum' => ['on', 'off', 'draft'],
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 4
            [
                'name' => 'email',
                'params' => [
                    'type' => 'string',
                    'format' => 'email',
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 5
            [
                'name' => 'url',
                'params' => [
                    'type' => 'string',
                    'format' => 'uri',
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 6
            [
                'name' => 'date',
                'params' => [
                    'type' => 'string',
                    'format' => 'date',
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 7
            [
                'name' => 'datetime',
                'params' => [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 8
            [
                'name' => 'number',
                'params' => ['type' => 'number'],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 9
            [
                'name' => 'integer',
                'params' => ['type' => 'integer'],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 10
            [
                'name' => 'boolean',
                'params' => ['type' => 'boolean'],
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 11
            [
                'name' => 'json',
                'params' => new \stdClass(),
                'created' => '2019-11-01 09:23:43',
                'modified' => '2019-11-01 09:23:43',
                'core_type' => true,
            ],
            // 12
            [
                'name' => 'unused property type',
                'params' => [
                    'type' => 'object',
                    'properties' => [
                        'gustavo' => ['const' => 'supporto'],
                    ],
                    'required' => ['gustavo'],
                ],
                'created' => '2019-11-02 09:23:43',
                'modified' => '2019-11-02 09:23:43',
                'core_type' => false,
            ],
            // 13
            [
                'name' => 'children_order',
                'params' => [
                    'type' => 'string',
                    'enum' => [
                        'position',
                        '-position',
                        'title',
                        '-title',
                        'created',
                        '-created',
                        'modified',
                        '-modified',
                        'publish_start',
                        '-publish_start',
                    ],
                ],
                'created' => '2022-12-01 15:35:21',
                'modified' => '2022-12-01 15:35:21',
                'core_type' => true,
            ],
        ];

        parent::init();
    }
}
