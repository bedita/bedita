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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior;
use BEdita\Core\ORM\Inheritance\TableInheritanceManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior
 */
class ClassTableInheritanceBehaviorTest extends TestCase
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
     * @var \Cake\ORM\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \Cake\ORM\Table
     */
    public $fakeFelines;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $options = ['className' => 'BEdita\Core\ORM\Table'];
        $this->fakeAnimals = TableRegistry::get('FakeAnimals', $options);
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals', $options);
        $this->fakeMammals->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeAnimals'
            ]
        ]);

        $this->fakeFelines = TableRegistry::get('FakeFelines', $options);
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeMammals'
            ]
        ]);
    }

    /**
     * Data provider for `testAddBehavior` test case.
     *
     * @return array
     */
    public function addBehaviorProvider()
    {
        return [
            'noConf' => [
                false,
                []
            ],
            'missingTableName' => [
                false,
                [
                    'table' => ['className' => 'Cake\ORM\Table']
                ]
            ],
            'confTable' => [
                [
                    'tableName' => 'FakeMammals',
                    'className' => null,
                ],
                [
                    'table' => ['tableName' => 'FakeMammals']
                ]
            ],
            'confTableAndClassName' => [
                [
                    'tableName' => 'FakeMammals',
                    'className' => 'Cake\ORM\Table',
                ],
                [
                    'table' => [
                        'tableName' => 'FakeMammals',
                        'className' => 'Cake\ORM\Table'
                    ]
                ]
            ]
        ];
    }

    /**
     * testAddBehavior method
     *
     * @return void
     *
     * @dataProvider addBehaviorProvider
     * @covers ::initialize()
     */
    public function testAddBehavior($expected, $conf)
    {
        TableInheritanceManager::removeTable($this->fakeFelines, 'FakeMammals');
        $this->fakeFelines->removeBehavior('ClassTableInheritance');

        if ($expected === false) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', $conf);
        $inheritedTable = current($this->fakeFelines->inheritedTables());

        $this->assertEquals($expected['tableName'], $inheritedTable->alias());
        $this->assertEquals($expected['className'], $this->fakeFelines->association($inheritedTable->alias())->className());
    }

    /**
     * Test basic find
     *
     * @return void
     * @coversNothing
     */
    public function testBasicFind()
    {
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
        $felines = $this->fakeFelines->find()->hydrate(false);
        $this->assertEquals(1, $felines->count());

        $result = $felines->first();
        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);

        // find mammals
        $mammals = $this->fakeMammals->find()->hydrate(false);
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
     * @covers ::patchContain()
     */
    public function testContainFind()
    {
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
     * @return void
     *
     * @dataProvider saveProvider
     * @covers ::beforeSave()
     * @covers \BEdita\Core\ORM\Association\ExtensionOf::saveAssociated()
     * @covers \BEdita\Core\ORM\Association\ExtensionOf::targetPropertiesValues()
     */
    public function testSave($expected, $data)
    {
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
     * @return void
     *
     * @dataProvider selectProvider
     * @coversNothing
     */
    public function testSelect($expected, $select)
    {
        static $allColumns = null;
        if ($allColumns === null) {
            $allColumns = $this->fakeFelines->schema()->columns();
            foreach ($this->fakeFelines->inheritedTables(true) as $t) {
                $allColumns = array_merge($allColumns, $t->schema()->columns());
            }
            $allColumns = array_unique($allColumns);
        }

        $unexpectedFields = array_diff($allColumns, $expected);

        $felines = $this->fakeFelines->find()->select($select);

        foreach ($felines as $f) {
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
     *
     * @coversNothing
     */
    public function testClauses()
    {
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
            ->hydrate(false);

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
     * @return void
     *
     * @dataProvider findListProvider
     * @coversNothing
     */
    public function testFindList($expected, $listParams, $order)
    {
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
