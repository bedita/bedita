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

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\Table;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Table as CakeTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Table} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Table
 */
class TableTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_mammals',
        'plugin.BEdita/Core.fake_felines',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeFelines;

    /**
     * Table options used for initialization
     *
     * @var array
     */
    protected $tableOptions = ['className' => 'BEdita\Core\ORM\Inheritance\Table'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeFelines = TableRegistry::get('FakeFelines', $this->tableOptions);
        $this->fakeMammals = TableRegistry::get('FakeMammals', $this->tableOptions);
        $this->fakeAnimals = TableRegistry::get('FakeAnimals', $this->tableOptions);
    }

    /**
     * Setup inheritance associations
     *
     * @return void
     */
    protected function setupAssociations()
    {
        $this->fakeMammals->extensionOf('FakeAnimals');
        $this->fakeFelines->extensionOf('FakeMammals');
        $this->fakeAnimals->hasMany('FakeArticles');
    }

    /**
     * test query
     *
     * @return void
     * @covers ::query()
     */
    public function testQuery()
    {
        $this->assertInstanceOf('\BEdita\Core\ORM\Inheritance\Query', $this->fakeFelines->query());
    }

    /**
     * test setup ExtensionOf association
     *
     * @return void
     * @covers ::extensionOf()
     */
    public function testExtensionOf()
    {
        $this->assertTrue(method_exists($this->fakeFelines, 'extensionOf'));
        $extensionOf = $this->fakeFelines->associations()->type('ExtensionOf');
        $this->assertCount(0, $extensionOf);
        $this->fakeFelines->extensionOf('FakeMammals', $this->tableOptions);
        $extensionOf = $this->fakeFelines->associations()->type('ExtensionOf');
        $this->assertCount(1, $extensionOf);

        $extensionOf = current($extensionOf);
        $this->assertEquals(TableRegistry::get('FakeMammals'), $extensionOf->target());

        // trying to add another extensionOf association on the same table
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessageRegExp('/.*has already an ExtensionOf association with.*/');
        $this->fakeFelines->extensionOf('FakeAnimals', $this->tableOptions);
    }

    /**
     * test implementedEvents to see when inheritance listeners are bound
     *
     * @return void
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        $implementedEvents = $this->fakeFelines->implementedEvents();
        $expected = [
            'Model.beforeFind' => 'inheritanceBeforeFind',
            'Model.beforeSave' => 'inheritanceBeforeSave',
        ];
        $this->assertEquals($expected, $implementedEvents);

        $mockTable = $this->getMockBuilder($this->tableOptions['className'])
            ->setMethods([
                'beforeFind',
                'beforeSave',
            ])
            ->getMock();

        $implementedEvents = $mockTable->implementedEvents();
        $expected = [
            'Model.beforeFind' => [
                ['callable' => 'beforeFind'],
                ['callable' => 'inheritanceBeforeFind']
            ],
            'Model.beforeSave' => [
                ['callable' => 'beforeSave'],
                ['callable' => 'inheritanceBeforeSave']
            ],
        ];
        $this->assertEquals($expected, $implementedEvents);
    }

    /**
     * Data provider for testTriggerEvents
     *
     * @return array
     */
    public function triggerEventsProvider()
    {
        return [
            'beforeFind' => [
                [
                    'beforeFind' => 'called as first',
                    'inheritanceBeforeFind' => 'called as second'
                ],
                'Model.beforeFind'
            ],
            'beforeSave' => [
                [
                    'beforeSave' => 'called as first',
                    'inheritanceBeforeSave' => 'called as second'
                ],
                'Model.beforeSave'
            ]
        ];
    }

    /**
     * test that listeners are really triggered
     *
     * @param array $expected The expected results
     * @param string $eventName The even name to trigger
     * @return void
     * @dataProvider triggerEventsProvider
     * @covers ::implementedEvents()
     */
    public function testTriggerEvents($expected, $eventName)
    {
        $method = str_replace('Model.', '', $eventName);
        $iMethod = 'inheritance' . ucfirst($method);

        $mockTable = $this->getMockBuilder($this->tableOptions['className'])
            ->setMethods([
                'beforeFind',
                'beforeSave',
                'inheritanceBeforeFind',
                'inheritanceBeforeSave',
            ])
            ->getMock();

        $mockTable->expects($this->once())->method($method)
            ->will($this->returnValue([$method => 'called as first']));

        $callback = function ($event) use ($iMethod) {
            return array_merge($event->result, [$iMethod => 'called as second']);
        };
        $mockTable->expects($this->once())->method($iMethod)
            ->will($this->returnCallback($callback));

        if ($eventName == 'Model.beforeFind') {
            $mockQuery = $this->getMockBuilder('\BEdita\Core\ORM\Inheritance\Query')
                ->setConstructorArgs([null, null])
                ->getMock();

            $event = $mockTable->dispatchEvent('Model.beforeFind', [
                $mockQuery,
                new \ArrayObject(),
                true
            ]);
        } else {
            $event = $mockTable->dispatchEvent('Model.beforeSave', [
                $mockTable->newEntity(),
                new \ArrayObject()
            ]);
        }

        $this->assertEquals($expected, $event->result);
    }

    /**
     * Test inherited tables
     *
     * @return void
     * @covers ::inheritedTables()
     */
    public function testInheritedTables()
    {
        $this->assertEquals([], $this->fakeFelines->inheritedTables());

        $this->setupAssociations();

        $mammalsInheritance = current($this->fakeMammals->inheritedTables());
        $this->assertEquals('FakeAnimals', $mammalsInheritance->alias());

        $felinesInheritance = current($this->fakeFelines->inheritedTables());
        $this->assertEquals('FakeMammals', $felinesInheritance->alias());

        $felinesDeepInheritance = array_map(function (Table $inherited) {
            return $inherited->getAlias();
        }, $this->fakeFelines->inheritedTables(true));

        $this->assertEquals(['FakeMammals', 'FakeAnimals'], $felinesDeepInheritance);
    }

    /**
     * Test inherited tables
     *
     * @return void
     * @covers ::isTableInherited()
     */
    public function testIsTableInherited()
    {
        $this->assertFalse($this->fakeFelines->isTableInherited('FakeMammals'));
        $this->assertFalse($this->fakeFelines->isTableInherited('FakeMammals', true));

        $this->setupAssociations();
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeAnimals', true));
        $this->assertFalse($this->fakeFelines->isTableInherited('FakeAnimals'));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals', true));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals'));
    }

    /**
     * testBasicFindWithoutInheritance
     *
     * @return void
     * @covers ::inheritanceBeforeFind()
     */
    public function testBasicFindWithoutInheritance()
    {
        // find felines
        $felines = $this->fakeFelines->find();
        $this->assertEquals(1, $felines->count());

        $feline = $felines->first();
        $expected = [
            'id' => 1,
            'family' => 'purring cats'
        ];
        $result = $feline->extract($felines->first()->visibleProperties());
        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * testBasicFindWithInheritance
     *
     * @return void
     * @covers ::inheritanceBeforeFind()
     */
    public function testBasicFindWithInheritance()
    {
        $this->setupAssociations();

        // find felines
        $felines = $this->fakeFelines->find();
        $this->assertEquals(1, $felines->count());

        $feline = $felines->first();
        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'subclass' => 'Eutheria',
            'family' => 'purring cats'
        ];
        $result = $feline->extract($felines->first()->visibleProperties());
        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);

        $this->assertFalse($feline->dirty());

        // hydrate false
        $felines = $this->fakeFelines->find()->enableHydration(false);
        $this->assertEquals(1, $felines->count());

        $result = $felines->first();
        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);

        // find mammals
        $mammals = $this->fakeMammals->find()->enableHydration(false);
        $this->assertEquals(2, $mammals->count());

        $expected = [
            [
                'id' => 1,
                'name' => 'cat',
                'legs' => 4,
                'subclass' => 'Eutheria'
            ],
            [
                'id' => 2,
                'name' => 'koala',
                'legs' => 4,
                'subclass' => 'Marsupial'
            ]
        ];
        $expected = array_map(function ($a) {
            ksort($a);

            return $a;
        }, $expected);

        $result = array_map(function ($a) {
            ksort($a);

            return $a;
        }, $mammals->toArray());
        $this->assertEquals($expected, $result);
    }

    /**
     * Test find using contain
     *
     * @return void
     * @covers ::inheritanceBeforeFind()
     */
    public function testContainFind()
    {
        $this->setupAssociations();

        $felines = $this->fakeFelines
            ->find()
            ->contain('FakeArticles');
        $this->assertEquals(1, $felines->count());

        $feline = $felines->first();

        $this->assertTrue($feline->has('fake_articles'));
        $this->assertEquals(2, count($feline->fake_articles));
        $this->assertFalse($feline->dirty());

        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'subclass' => 'Eutheria',
            'family' => 'purring cats',
            'fake_articles' => [
                [
                    'id' => 1,
                    'title' => 'The cat',
                    'body' => 'article body',
                    'fake_animal_id' => 1
                ],
                [
                    'id' => 2,
                    'title' => 'Puss in boots',
                    'body' => 'text',
                    'fake_animal_id' => 1
                ]
            ]
        ];
        ksort($expected);

        $result = $feline->toArray();
        ksort($result);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'onlyAncestorField' => [
                [
                    'id' => 4,
                    'name' => 'lion'
                ],
                [
                    'name' => 'lion'
                ]
            ],
            'noAncestorsField' => [
                [
                    'id' => 4,
                    'family' => 'big cats'
                ],
                [
                    'family' => 'big cats'
                ]
            ],
            'noParentField' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'family' => 'big cats'
                ],
                [
                    'name' => 'tiger',
                    'family' => 'big cats'
                ]
            ],
            'simple' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats'
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats'
                ]
            ],
            'advanced' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        [
                            'id' => 1,
                            'title' => 'The cat',
                            'body' => 'article body',
                            'fake_animal_id' => 4
                        ],
                        [
                            'id' => 2,
                            'title' => 'Puss in boots',
                            'body' => 'text',
                            'fake_animal_id' => 4
                        ]
                    ]
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        '_ids' => [1, 2]
                    ]
                ]
            ],
            'advancedNewArticles' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        [
                            'id' => 3,
                            'title' => 'The white tiger',
                            'body' => 'Body of article',
                            'fake_animal_id' => 4
                        ],
                        [
                            'id' => 4,
                            'title' => 'Sandokan',
                            'body' => 'The Malaysian tiger',
                            'fake_animal_id' => 4
                        ]
                    ]
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        [
                            'title' => 'The white tiger',
                            'body' => 'Body of article'
                        ],
                        [
                            'title' => 'Sandokan',
                            'body' => 'The Malaysian tiger'
                        ]
                    ]
                ]
            ],
            'simplePatch' => [
                [
                    'id' => 1,
                    'name' => 'The super cat',
                    'family' => 'purring cats',
                    'legs' => 4,
                    'subclass' => 'None',
                ],
                [
                    'id' => 1,
                    'name' => 'The super cat',
                    'subclass' => 'None',
                ]
            ],
        ];
    }

    /**
     * testSave method
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider saveProvider
     * @covers ::inheritanceBeforeSave()
     * @covers \BEdita\Core\ORM\Association\ExtensionOf::saveAssociated()
     * @covers \BEdita\Core\ORM\Association\ExtensionOf::targetPropertiesValues()
     */
    public function testSave($expected, $data)
    {
        $this->setupAssociations();

        $feline = $this->fakeFelines->newEntity();
        if (!empty($data['id'])) {
            $feline = $this->fakeFelines->get($data['id']);
        }
        $feline = $this->fakeFelines->patchEntity($feline, $data);
        $result = $this->fakeFelines->save($feline);

        $this->assertNotFalse($result);
        $resultArray = $result->toArray();
        $this->assertEquals($expected, $resultArray);

        $this->assertCount(1, $this->fakeFelines->findById($result->id));
        $this->assertCount(1, $this->fakeMammals->findById($result->id));
        $this->assertCount(1, $this->fakeAnimals->findById($result->id));
    }

    /**
     * Data provider for `testFixClause` test case.
     *
     * @return array
     */
    public function selectProvider()
    {
        return [
            'fieldsFromAllInherited' => [
                ['family', 'subclass', 'name'],
                ['family', 'subclass', 'name']
            ],
            'fieldsFromAncestor' => [
                ['name'],
                ['name']
            ],
            'fieldsFromParent' => [
                ['subclass'],
                ['subclass']
            ],
        ];
    }

    /**
     * testSelect
     *
     * @param array $expected Expected result.
     * @param array $select Select clause.
     * @return void
     *
     * @dataProvider selectProvider
     * @covers ::inheritanceBeforeFind()
     * @covers \BEdita\Core\ORM\Inheritance\ResultSet::_calculateColumnMap()
     * @covers \BEdita\Core\ORM\Inheritance\ResultSet::_groupResult()
     * @covers \BEdita\Core\ORM\Association\ExtensionOf::transformRow()
     */
    public function testSelect($expected, $select)
    {
        $this->setupAssociations();

        $allColumns = $this->fakeFelines->getSchema()->columns();
        foreach ($this->fakeFelines->inheritedTables(true) as $t) {
            if (!($t instanceof CakeTable)) {
                $this->fail('Unexpected table object');
            }

            $allColumns = array_merge($allColumns, $t->getSchema()->columns());
        }
        $allColumns = array_unique($allColumns);

        $unexpectedFields = array_diff($allColumns, $expected);

        $felines = $this->fakeFelines->find()->select($select);

        foreach ($felines as $f) {
            if (!($f instanceof EntityInterface)) {
                $this->fail('Unexpected entity');
            }

            foreach ($expected as $field) {
                $this->assertTrue($f->has($field));
            }

            foreach ($unexpectedFields as $field) {
                $this->assertFalse($f->has($field));
            }
        }
    }

    /**
     * testClauses
     *
     * @return void
     */
    public function testClauses()
    {
        $this->setupAssociations();

        // add some row
        $data = [
            'legs' => 4,
            'subclass' => 'Another Sublcass',
            'family' => 'big cats'
        ];

        foreach (['tiger', 'lion', 'leopard'] as $animal) {
            $data['name'] = $animal;
            $feline = $this->fakeFelines->newEntity($data);
            $this->fakeFelines->save($feline);
        }

        $query = $this->fakeFelines->find();
        $result = $query->select(['subclass', 'count' => $query->func()->count('*')])
            ->group(['subclass'])
            ->enableHydration(false);

        foreach ($result as $item) {
            if ($item['subclass'] == 'Eutheria') {
                $this->assertEquals(1, $item['count']);
            } elseif ($item['subclass'] == 'Another Sublcass') {
                $this->assertEquals(3, $item['count']);
            }
        }
    }

    /**
     * Provider for `testFindList`
     *
     * @return array
     */
    public function findListProvider()
    {
        return [
            'fieldsOnMain' => [
                [
                    1 => 'purring cats',
                    4 => 'big cats',
                    5 => 'big cats',
                    6 => 'big cats',
                ],
                [
                    'keyField' => 'id',
                    'valueField' => 'family'
                ],
                ['id' => 'asc']
            ],
            'fieldsOnMainAndParent' => [
                [
                    1 => 'Eutheria',
                    4 => 'Another Sublcass',
                    5 => 'Another Sublcass',
                    6 => 'Another Sublcass',
                ],
                [
                    'keyField' => 'id',
                    'valueField' => 'subclass'
                ],
                ['id' => 'asc']
            ],
            'fieldsOnParentAndAncestor' => [
                [
                    'cat' => 'Eutheria',
                    'leopard' => 'Another Sublcass',
                    'lion' => 'Another Sublcass',
                    'tiger' => 'Another Sublcass',
                ],
                [
                    'keyField' => 'name',
                    'valueField' => 'subclass'
                ],
                ['name' => 'asc']
            ],
            'fieldsOnAncestor' => [
                [
                    'cat' => 4,
                    'leopard' => 4,
                    'lion' => 4,
                    'tiger' => 4,
                ],
                [
                    'keyField' => 'name',
                    'valueField' => 'legs'
                ],
                ['name' => 'asc']
            ]
        ];
    }

    /**
     * testFindList
     *
     * @param array $expected Expected results.
     * @param array $listParams Options for `find('list')`.
     * @param array $order Order clause.
     * @return void
     *
     * @dataProvider findListProvider
     */
    public function testFindList($expected, $listParams, $order)
    {
        $this->setupAssociations();

        // add some row
        $data = [
            'legs' => 4,
            'subclass' => 'Another Sublcass',
            'family' => 'big cats'
        ];

        foreach (['tiger', 'lion', 'leopard'] as $animal) {
            $data['name'] = $animal;
            $feline = $this->fakeFelines->newEntity($data);
            $this->fakeFelines->save($feline);
        }

        $query = $this->fakeFelines->find('list', $listParams);
        $query->order($order);

        $result = $query->toArray();
        $this->assertEquals($expected, $result);
    }
}
