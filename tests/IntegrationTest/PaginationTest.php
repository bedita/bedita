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

namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;

/**
 * Test on paginator options.
 */
class PaginationTest extends IntegrationTestCase
{

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.media',
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
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 8,
                    'page_size' => 20,
                ],
            ],
            'lower' => [
                [
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 2,
                    'page_items' => 5,
                    'page_size' => 5,
                ],
                [
                    'limit' => 5,
                    'maxLimit' => 10,
                ],
            ],
            'higher' => [
                [
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 8,
                    'page_size' => 50,
                ],
                [
                    'limit' => 50,
                ],
            ],
            'too  high' => [
                [
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 8,
                    'page_size' => 100,
                ],
                [
                    'limit' => 1000,
                    'maxLimit' => 1000,
                ],
            ],
        ];
    }

    /**
     * Test that pagination options are applied correctly..
     *
     * @param array $expected Expected pagination.
     * @param array $options Pagination options.
     * @return void
     *
     * @dataProvider optionsProvider
     * @coversNothing
     */
    public function testOptions(array $expected, array $options = [])
    {
        Configure::write('Pagination', $options);

        $this->configRequestHeaders('GET');
        $this->get('/objects');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        static::assertArrayHasKey('meta', $body);
        static::assertArrayHasKey('pagination', $body['meta']);
        static::assertEquals($expected, $body['meta']['pagination']);
    }
}
