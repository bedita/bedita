<?php
declare(strict_types=1);

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

use Cake\TestSuite\Fixture\TestFixture;

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
        // 1
        [
            'name' => 'test',
            'label' => 'Test relation',
            'inverse_name' => 'inverse_test',
            'inverse_label' => 'Inverse test relation',
            'description' => 'Sample description.',
            'params' => null,
        ],
        // 2
        [
            'name' => 'another_test',
            'label' => 'Another test relation',
            'inverse_name' => 'inverse_another_test',
            'inverse_label' => 'Another inverse test relation',
            'description' => 'Sample description /2.',
            'params' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                    ],
                    'age' => [
                        'type' => 'integer',
                        'minimum' => 0,
                    ],
                ],
                'required' => ['name'],
            ],
        ],
        // 3
        [
            'name' => 'test_abstract',
            'label' => 'Test relation involving abstract types',
            'inverse_name' => 'inverse_test_abstract',
            'inverse_label' => 'Inverse test relation involving abstract types',
            'description' => 'Sample description.',
            'params' => null,
        ],
        // 4
        [
            'name' => 'test_simple',
            'label' => 'Test relation involving simple parameters',
            'inverse_name' => 'inverse_test_simple',
            'inverse_label' => 'Inverse test relation involving simple parameters',
            'description' => 'Sample description.',
            'params' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                    ],
                    'age' => [
                        'type' => 'integer',
                        'minimum' => 0,
                    ],
                ],
            ],
        ],
        // 5
        [
            'name' => 'test_defaults',
            'label' => 'Test relation involving default parameters',
            'inverse_name' => 'inverse_test_defaults',
            'inverse_label' => 'Inverse test relation involving default parameters',
            'description' => 'Sample description.',
            'params' => [
                'type' => 'object',
                'properties' => [
                    'size' => [
                        'type' => 'integer',
                        'default' => 5,
                    ],
                    'street' => [
                        'type' => 'string',
                        'default' => 'fighter',
                    ],
                    'color' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ],
    ];
}
