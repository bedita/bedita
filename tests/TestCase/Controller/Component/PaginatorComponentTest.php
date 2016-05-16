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

namespace BEdita\API\Test\TestCase\Controller\Component;

use BEdita\API\Controller\Component\PaginatorComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\Component\PaginatorComponent
 */
class PaginatorComponentTest extends TestCase
{
    /**
     * Data provider for `testMergeOptions` test case.
     *
     * @return array
     */
    public function mergeOptionsProvider()
    {
        return [
            'default' => [
                [
                    'page' => 1,
                    'limit' => 20,
                    'maxLimit' => 100,
                    'whitelist' => ['page', 'page_size', 'sort'],
                ],
                [],
                'MyModel',
            ],
            'customLimit' => [
                [
                    'page' => 1,
                    'limit' => 10,
                    'maxLimit' => 100,
                    'whitelist' => ['page', 'page_size', 'sort'],
                ],
                [
                    'limit' => 5,
                ],
                'MyModel',
                [
                    'limit' => 10,
                    'maxLimit' => 100,
                ],
            ],
            'customPageSize' => [
                [
                    'page' => 1,
                    'limit' => 5,
                    'maxLimit' => 100,
                    'whitelist' => ['page', 'page_size', 'sort'],
                ],
                [
                    'page_size' => 5,
                ],
                'MyModel',
                [
                    'limit' => 10,
                    'maxLimit' => 100,
                ],
            ],
            'noOverride' => [
                [
                    'page' => 1,
                    'limit' => 10,
                    'maxLimit' => 100,
                    'whitelist' => ['page'],
                ],
                [
                    'page_size' => 5,
                ],
                'MyModel',
                [
                    'limit' => 10,
                    'maxLimit' => 100,
                ],
                ['page'],
            ],
        ];
    }

    /**
     * Test `mergeOptions()` method.
     *
     * @param array $expected Expected result.
     * @param array $query Query params.
     * @param string $alias Model alias.
     * @param array $settings Additional settings.
     * @param array|null $whitelist Overridable configuration items whitelist.
     * @return void
     *
     * @dataProvider mergeOptionsProvider
     * @covers ::mergeOptions()
     */
    public function testMergeOptions(array $expected, array $query, $alias, array $settings = [], array $whitelist = null)
    {
        $request = new Request(compact('query'));
        $component = new PaginatorComponent(new ComponentRegistry(new Controller($request)), []);
        if ($whitelist) {
            $component->config('whitelist', $whitelist, false);
        }

        $this->assertEquals($expected, $component->mergeOptions($alias, $settings));
    }
}
