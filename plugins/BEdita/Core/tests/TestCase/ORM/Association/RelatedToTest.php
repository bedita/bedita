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

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\ORM\Association\RelatedTo
 */
class RelatedToTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.ObjectRelations',
    ];

    /**
     * Data provider for {@see RelatedToTest::testGetSubQueryForMatching()} test case.
     *
     * @return array[]
     */
    public function getSubQueryForMatchingProvider(): array
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
    public function testGetSubQueryForMatching(array $expected, string $table, string $association, array $options = []): void
    {
        $table = TableRegistry::getTableLocator()->get($table);
        $association = $table->getAssociation($association);
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

    /**
     * Data provider for {@see RelatedToTest::testIsSourceAbstract()} test case.
     *
     * @return array[]
     */
    public function isAbstractProvider(): array
    {
        return [
            'abstract' => [
                true,
                'Objects',
            ],
            'concrete' => [
                false,
                'Profiles',
            ],
            'concreteBecauseNotAnObjectType' => [
                false,
                'Relations',
            ],
        ];
    }

    /**
     * Test if source table is abstract
     *
     * @param bool $expected The expected value
     * @param string $table The source table name
     * @return void
     *
     * @dataProvider isAbstractProvider
     * @covers ::isSourceAbstract()
     * @covers ::isAbstract()
     */
    public function testIsSourceAbstract(bool $expected, string $table): void
    {
        $relatedTo = new RelatedTo('SourceAbstract');
        $relatedTo->setSource(TableRegistry::getTableLocator()->get($table));
        static::assertSame($expected, $relatedTo->isSourceAbstract());
    }

    /**
     * Test if target table is abstract
     *
     * @param bool $expected The expected value
     * @param string $table The target table name
     * @return void
     *
     * @dataProvider isAbstractProvider
     * @covers ::isTargetAbstract()
     * @covers ::isAbstract()
     */
    public function testIsTargetAbstract(bool $expected, string $table): void
    {
        $relatedTo = new RelatedTo('SourceAbstract');
        $relatedTo->setTarget(TableRegistry::getTableLocator()->get($table));
        static::assertSame($expected, $relatedTo->isTargetAbstract());
    }

    /**
     * Data provider for {@see RelatedToTest::testIsInverse()} test case.
     *
     * @return array[]
     */
    public function isInverseProvider(): array
    {
        return [
            'direct' => [
                false,
                [
                    'foreignKey' => 'left_id',
                ],
            ],
            'inverse' => [
                true,
                [
                    'foreignKey' => 'right_id',
                ],
            ],
            'inverseCustom' => [
                true,
                [
                    'foreignKey' => 'left_id',
                    'inverseKey' => 'left_id',
                ],
            ],
            'inverseMultiCustom' => [
                true,
                [
                    'foreignKey' => ['left_id', 'custom_key'],
                    'inverseKey' => ['left_id', 'custom_key'],
                ],
            ],
        ];
    }

    /**
     * Test if related association is inverse.
     *
     * @param bool $expected The value expected.
     * @param array $options The options for the association.
     * @return void
     *
     * @dataProvider isInverseProvider()
     * @covers ::isInverse()
     * @covers ::_options()
     * @covers ::setInverseKey()
     * @covers ::getInverseKey()
     */
    public function testIsInverse(bool $expected, array $options): void
    {
        $relatedTo = new RelatedTo('Alias', $options);
        static::assertEquals($expected, $relatedTo->isInverse());
    }

    /**
     * Test setting and retrieving object type.
     *
     * @return void
     *
     * @covers ::_options()
     * @covers ::setObjectType()
     * @covers ::getObjectType()
     */
    public function testSetGetObjectTypeNull(): void
    {
        $relatedTo = new RelatedTo('Alias', ['objectType' => null]);
        static::assertNull($relatedTo->getObjectType());
    }

    /**
     * Test setting and retrieving object type.
     *
     * @return void
     *
     * @covers ::_options()
     * @covers ::setObjectType()
     * @covers ::getObjectType()
     */
    public function testSetGetObjectType(): void
    {
        $objectType = $this->getTableLocator()->get('ObjectTypes')
            ->get(1);
        $relatedTo = new RelatedTo('Alias', compact('objectType'));

        static::assertSame($objectType, $relatedTo->getObjectType());
    }

    /**
     * Data provider for {@see RelatedToTest::testGetTarget()} test case.
     *
     * @return array[]
     */
    public function getTargetProvider(): array
    {
        return [
            'no object type set' => [null, [], 'BEdita/Core.Objects', null],
            'not an object table' => [null, [], 'BEdita/Core.ObjectTypes', 'locations'],
            'profiles' => ['profiles', ['inversetest'], 'BEdita/Core.Profiles', 'profiles'],
            'override' => ['events', ['test', 'inversetest', 'testabstract'], null, 'events', 'Documents'],
        ];
    }

    /**
     * Test loading correct relations on target table.
     *
     * @param string|null $expectedOT Expected object type name on target table.
     * @param string[] $expectedAssociations Expected associations set on target table.
     * @param string|null $className Class name of the target table.
     * @param string|null $objectType Object type to pass to association.
     * @param string $alias Relation alias.
     * @return void
     *
     * @dataProvider getTargetProvider()
     * @covers ::getTarget()
     */
    public function testGetTarget(?string $expectedOT, array $expectedAssociations, ?string $className, ?string $objectType, string $alias = 'Alias'): void
    {
        $options = compact('className');
        if ($objectType !== null) {
            $options['objectType'] = $this->getTableLocator()->get('ObjectTypes')
                ->find()
                ->clearContain()
                ->where(['name' => $objectType])
                ->firstOrFail();

            // Ensure that relations aren't present in the ObjectType entity, to test that they will be loaded by RelatedTo::getTarget().
            static::assertEmpty($options['objectType']['left_relations']);
            static::assertEmpty($options['objectType']['right_relations']);
        }
        $relatedTo = new RelatedTo($alias, $options);

        $target = $relatedTo->getTarget();
        static::assertInstanceOf(Table::class, $target);

        $actualAssociations = $target->associations()->keys();
        foreach ($expectedAssociations as $expectedAssoc) {
            static::assertContains($expectedAssoc, $actualAssociations);
        }
        if ($expectedOT !== null) {
            $actualOT = $target->objectType();
            static::assertInstanceOf(ObjectType::class, $actualOT);
            static::assertSame($expectedOT, $actualOT->name);
        }
    }
}
