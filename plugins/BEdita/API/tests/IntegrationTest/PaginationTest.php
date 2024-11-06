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

namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;

/**
 * Test on paginator options.
 */
class PaginationTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
    ];

    /**
     * Data provider for `testOptions` test case.
     *
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'default' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 16,
                    'page_size' => 20,
                ],
            ],
            'lower' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 4,
                    'page_items' => 5,
                    'page_size' => 5,
                ],
                [
                    'limit' => 5,
                ],
            ],
            // set 10 as maxLimit, page_size of 20 not allowed
            'low max limit' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 2,
                    'page_items' => 10,
                    'page_size' => 10,
                ],
                [
                    'limit' => 5,
                    'maxLimit' => 10,
                ],
                'page_size=20',
            ],
            'higher' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 16,
                    'page_size' => 50,
                ],
                [
                    'limit' => 50,
                ],
            ],
            // set 200 as maxLimit, page_size of 200 allowed
            'increase max' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 16,
                    'page_size' => 200,
                ],
                [
                    'maxLimit' => 200,
                ],
                'page_size=200',
            ],
            // try to set an invalid limit of 1000
            // BEdita\API\Datasource\JsonApiPaginator::MAX_LIMIT (500) is used instead
            'too high' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 16,
                    'page_size' => 500,
                ],
                [
                    'limit' => 1000,
                    'maxLimit' => 1000,
                ],
                'page_size=600',
            ],
            // set 500 as maxLimit, page_size of 300 is allowed
            'not too high' => [
                [
                    'count' => 16,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 16,
                    'page_size' => 300,
                ],
                [
                    'maxLimit' => 500,
                ],
                'page_size=300',
            ],
        ];
    }

    /**
     * Test that pagination options are applied correctly.
     *
     * @param array $expected Expected pagination.
     * @param array $options Pagination options.
     * @return void
     * @dataProvider optionsProvider
     * @coversNothing
     */
    public function testOptions(array $expected, array $options = [], string $query = '')
    {
        Configure::write('Pagination', $options);

        $this->configRequestHeaders('GET');
        $this->get('/objects?' . $query);

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        static::assertArrayHasKey('meta', $body);
        static::assertArrayHasKey('pagination', $body['meta']);
        static::assertEquals($expected, $body['meta']['pagination']);
    }
}
