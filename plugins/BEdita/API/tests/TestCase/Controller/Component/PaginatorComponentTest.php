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
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\Component\PaginatorComponent
 */
class PaginatorComponentTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public $autoFixtures = false;

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

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

        $options = $component->mergeOptions($alias, $settings);
        $this->assertEquals($expected, $options);
    }

    /**
     * Data provider for `testValidateSort` test case.
     *
     * @return array
     */
    public function validateSortProvider()
    {
        return [
            'default' => [
                [],
            ],
            'implicitAsc' => [
                ['Users.username' => 'asc'],
                'username',
            ],
            'explicitAsc' => [
                ['Users.username' => 'asc'],
                '+username',
            ],
            'desc' => [
                ['Users.username' => 'desc'],
                '-username',
            ],
            'multipleFields' => [
                false,
                'username,created',
            ],
            'unallowedField' => [
                false,
                '-this_field_does_not_exist',
            ],
        ];
    }

    /**
     * Test `validateSort()` method.
     *
     * @param array|false $expected Expected result.
     * @param string|null $sort `sort` query parameter in request.
     * @return void
     *
     * @dataProvider validateSortProvider
     * @covers ::validateSort()
     */
    public function testValidateSort($expected, $sort = null)
    {
        $this->loadFixtures('Users');

        if ($expected === false) {
            $this->setExpectedException('Cake\Network\Exception\BadRequestException');
        }

        $request = new Request(['query' => compact('sort')]);
        $component = new PaginatorComponent(new ComponentRegistry(new Controller($request)), []);

        $repository = TableRegistry::get('Users')->find()->repository();
        $options = $component->mergeOptions('Users', []);

        $options = $component->validateSort($repository, $options);
        $this->assertEquals($expected, $options['order']);
    }
}
