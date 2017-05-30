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

namespace BEdita\Core\Test\TestCase\ORM\Association;

use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\ORM\Association\RelatedTo
 */
class RelatedToTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.object_relations',
    ];

    /**
     * Data provider for `testGetSubQueryForMatching` test case.
     *
     * @return array
     */
    public function getSubQueryForMatchingProvider()
    {
        return [
            'simple' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                ],
                'Documents',
                'Test',
            ],
            'simple (inverse)' => [
                [
                    4 => 'Gustavo',
                ],
                'Profiles',
                'InverseTest',
            ],
            'with conditions' => [
                [
                    2 => 'title one',
                ],
                'Documents',
                'Test',
                [
                    'conditions' => [
                        'Test.title' => 'title two',
                    ],
                ],
            ],
            'with query builder' => [
                [
                    2 => 'title one',
                ],
                'Documents',
                'Test',
                [
                    'queryBuilder' => function (Query $query) {
                        return $query->where([
                            'Test.title' => 'title two',
                        ]);
                    },
                ],
            ],
        ];
    }

    /**
     * Test method to obtain sub-query for matching.
     *
     * @param array $expected Expected result.
     * @param string $table Table name.
     * @param string $association Association name.
     * @param array $options Additional options.
     * @return void
     *
     * @dataProvider getSubQueryForMatchingProvider()
     * @covers ::getSubQueryForMatching()
     */
    public function testGetSubQueryForMatching(array $expected, $table, $association, array $options = [])
    {
        $table = TableRegistry::get($table);
        $association = $table->association($association);
        if (!($association instanceof RelatedTo)) {
            static::fail('Wrong association type');

            return;
        }

        $subQuery = $association->getSubQueryForMatching($options);

        static::assertInstanceOf(Query::class, $subQuery);

        $result = $table->find('list')
            ->where(function (QueryExpression $exp) use ($table, $subQuery) {
                return $exp->in($table->aliasField($table->getPrimaryKey()), $subQuery);
            })
            ->toArray();

        static::assertEquals($expected, $result);
    }
}
