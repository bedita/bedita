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
namespace BEdita\API\Test\TestCase\Datasource;

use BEdita\API\Datasource\JsonApiPaginator;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\OrderClauseExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\API\Datasource\JsonApiPaginator} Test Case
 */
#[CoversClass(JsonApiPaginator::class)]
class JsonApiPaginatorTest extends TestCase
{
    /**
     * Fixtures.
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Roles',
    ];

    /**
     * Data provider for `testMergeOptions` test case.
     *
     * @return array
     */
    public static function checkLimitProvider(): array
    {
        return [
            'default' => [
                [
                    'page' => 1,
                    'limit' => 20,
                    'maxLimit' => 100,
                    'allowedParameters' => ['page', 'page_size', 'sort'],
                    'sortableFields' => null,
                    'finder' => 'all',
                    'scope' => null,
                ],
                [],
            ],
            'customLimit' => [
                [
                    'page' => 1,
                    'limit' => 5,
                    'maxLimit' => 100,
                    'allowedParameters' => ['page', 'page_size', 'sort'],
                    'sortableFields' => null,
                    'finder' => 'all',
                    'scope' => null,
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
                    'allowedParameters' => ['page', 'page_size', 'sort'],
                    'sortableFields' => null,
                    'finder' => 'all',
                    'scope' => null,
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
     */
    #[DataProvider('checkLimitProvider')]
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
    public static function validateSortProvider(): array
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
            'special' => [
                ['date_ranges_max_end_date' => 'desc'],
                '-date_ranges_max_end_date',
            ],
            'published desc' => [
                new OrderClauseExpression(
                    new FunctionExpression('COALESCE', ['Roles.publish_start' => 'identifier', 'Roles.created' => 'identifier'], ['timestamp', 'timestamp'], 'timestamp'),
                    'desc',
                ),
                '-published',
            ],
            'published asc' => [
                new OrderClauseExpression(
                    new FunctionExpression('COALESCE', ['Roles.publish_start' => 'identifier', 'Roles.created' => 'identifier'], ['timestamp', 'timestamp'], 'timestamp'),
                    'asc',
                ),
                'published',
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
     */
    #[DataProvider('validateSortProvider')]
    public function testValidateSort($expected, $sort = null)
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $paginator = new JsonApiPaginator();
        $repository = TableRegistry::getTableLocator()->get('Roles')->find()->getRepository();

        $options = $paginator->validateSort($repository, compact('sort'));

        static::assertEquals($expected, $options['order']);
    }

    /**
     * Test `paginate()` method.
     */
    public function testPaginate()
    {
        $paginator = new JsonApiPaginator();

        $query = TableRegistry::getTableLocator()->get('Roles')->find()->orderBy('id');
        $params = ['sort' => '-name'];
        $res = $paginator->paginate($query, $params);

        // using 'id' order we should have 'first role', 'second role'
        // but '-name' order must prevail and invert above items
        $names = Hash::extract($res->toArray(), '{n}.name');
        static::assertEquals(['second role', 'first role'], $names);
    }
}
