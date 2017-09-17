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

namespace BEdita\API\Test\TestCase\Datasource;

use BEdita\API\Datasource\JsonApiPaginator;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Datasource\JsonApiPaginator
 */
class JsonApiPaginatorTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
    ];

    /**
     * Data provider for `testMergeOptions` test case.
     *
     * @return array
     */
    public function checkLimitProvider()
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
            ],
            'customLimit' => [
                [
                    'page' => 1,
                    'limit' => 5,
                    'maxLimit' => 100,
                    'whitelist' => ['page', 'page_size', 'sort'],
                ],
                [
                    'limit' => 5,
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
            ],
        ];
    }

    /**
     * Test `checkLimit()` method.
     *
     * @param array $expected Expected result.
     * @param array $options Paginator options.
     * @return void
     *
     * @dataProvider checkLimitProvider()
     * @covers ::checkLimit()
     */
    public function testCheckLimit(array $expected, array $options)
    {
        $paginator = new JsonApiPaginator();

        $options = $paginator->checkLimit($options + $paginator->getConfig());

        static::assertEquals($expected, $options);
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
            'asc' => [
                ['Roles.name' => 'asc'],
                'name',
            ],
            'desc' => [
                ['Roles.name' => 'desc'],
                '-name',
            ],
            'multipleFields' => [
                new BadRequestException('Unsupported sorting field'),
                'username,created',
            ],
            'unallowedField' => [
                new BadRequestException('Unsupported sorting field'),
                '-this_field_does_not_exist',
            ],
            'explicitAsc' => [
                new BadRequestException('Unsupported sorting field'),
                '+name',
            ],
        ];
    }

    /**
     * Test `validateSort()` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param string|null $sort `sort` query parameter in request.
     * @return void
     *
     * @dataProvider validateSortProvider()
     * @covers ::validateSort()
     */
    public function testValidateSort($expected, $sort = null)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $paginator = new JsonApiPaginator();
        $repository = TableRegistry::get('Roles')->find()->repository();

        $options = $paginator->validateSort($repository, compact('sort'));

        static::assertEquals($expected, $options['order']);
    }
}
