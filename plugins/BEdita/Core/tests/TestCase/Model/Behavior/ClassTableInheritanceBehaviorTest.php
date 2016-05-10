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
        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals');
        $this->fakeMammals->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'name' => 'FakeAnimals'
            ]
        ]);

        $this->fakeFelines = TableRegistry::get('FakeFelines');
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'name' => 'FakeMammals'
            ]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->fakeAnimals);
        unset($this->fakeMammals);
        unset($this->fakeFelines);
        TableRegistry::clear();

        parent::tearDown();
    }

    /**
     * Helper method to completely remove behavior
     *
     * @param \Cake\ORM\Table
     * @return void
     */
    protected function removeBehavior($table)
    {
        $cti = $table->behaviors()->get('ClassTableInheritance');
        foreach ($table->inheritedTables() as $inheritConf) {
            $cti->removeTable($inheritConf['name']);
        }
        $table->removeBehavior('ClassTableInheritance');
    }

    /**
     * Data provider for `testInitialize` test case.
     *
     * @return array
     */
    public function initializeProvider()
    {
        return [
            'noConf' => [
                null,
                []
            ],
            'missingTableName' => [
                null,
                [
                    'table' => ['className' => 'Cake\ORM\Table']
                ]
            ],
            'confTable' => [
                [
                    'name' => 'FakeMammals',
                    'className' => null,
                    'associationCreated' => true
                ],
                [
                    'table' => ['name' => 'FakeMammals']
                ]
            ],
            'confTableAndClassName' => [
                [
                    'name' => 'FakeMammals',
                    'className' => 'Cake\ORM\Table',
                    'associationCreated' => true
                ],
                [
                    'table' => [
                        'name' => 'FakeMammals',
                        'className' => 'Cake\ORM\Table'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test initialize
     *
     * @return void
     *
     * @dataProvider initializeProvider
     * @covers ::initialize()
     */
    public function testInitialize($expected, $conf)
    {
        $this->removeBehavior($this->fakeFelines);
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', $conf);
        $ctiConf = $this->fakeFelines->behaviors()->get('ClassTableInheritance')->config('table');
        $this->assertEquals($expected, $ctiConf);
    }

    /**
     * Data provider for `testRemoveTable` test case.
     *
     * @return array
     */
    public function removeTableProvider()
    {
        return [
            'notInherited' => [
                false,
                false,
                'FakeTable',
            ],
            'nestedInherited' => [
                false,
                false,
                'FakeAnimals',
            ],
            'associationCreated' => [
                null,
                false,
                'FakeMammals'
            ],
            'associationAlreadyPresent' => [
                null,
                true,
                'FakeMammals'
            ]
        ];
    }

    /**
     * Test removeTable
     *
     * @return void
     *
     * @dataProvider removeTableProvider
     * @covers ::removeTable()
     * @covers ::isTableInherited()
     * @covers ::inheritedTables()
     */
    public function testRemoveTable($expected, $createAssociationFirst, $tableName)
    {
        $cti = $this->fakeFelines->behaviors()->get('ClassTableInheritance');

        if ($createAssociationFirst === true) {
            $cti->removeTable($tableName);
            $this->fakeFelines->belongsTo($tableName, [
                'foreignKey' => 'id',
                'joinType' => 'INNER'
            ]);
            $cti->addTable('FakeMammals');
        }

        if ($expected === false) {
            $this->setExpectedException('\RuntimeException');
        }

        $this->assertEquals($cti, $cti->removeTable($tableName));
        $this->assertEquals($expected, $cti->config('table'));
        $this->assertEquals([], $this->fakeFelines->inheritedTables(true));

        if ($createAssociationFirst === true) {
             $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->fakeFelines->association($tableName));
        } else {
            $this->assertNull($this->fakeFelines->association($tableName));
        }
    }

    /**
     * Data provider for `testAddTable` test case.
     *
     * @return array
     */
    public function addTableProvider()
    {
        return [
            'behaviorAlreadyConfigured' => [
                false,
                false,
                'FakeTable',
            ],
            'tableAlreadyInherited' => [
                [
                    'name' => 'FakeAnimals',
                    'associationCreated' => true
                ],
                true,
                'FakeAnimals',
            ],
        ];
    }

    /**
     * Test addTable
     *
     * @return void
     *
     * @dataProvider addTableProvider
     * @covers ::addTable()
     */
    public function testAddTable($expected, $removeFirst, $tableName, $conf = [])
    {
        $cti = $this->fakeFelines->behaviors()->get('ClassTableInheritance');
        if ($removeFirst === true) {
            $cti->removeTable('FakeMammals');
        }

        if ($expected === false) {
            $this->setExpectedException('\RuntimeException');
        }

        $this->assertEquals($cti, $cti->addTable($tableName, $conf));
        $ctiConf = $cti->config('table');
        $this->assertEquals(sort($expected), sort($ctiConf));
    }

    /**
     * Data provider for `AddTableAlreadyAssociated` test case.
     *
     * @return array
     */
    public function addTableAlreadyAssociatedProvider()
    {
        return [
            'associationOk' => [
                [
                    'name' => 'FakeMammals',
                    'className' => null,
                    'associationCreated' => false
                ],
                'belongsTo',
                [
                    'foreignKey' => 'id',
                    'joinType' => 'INNER'
                ],
            ],
            'wrongAssociationType' => [
                false,
                'hasOne',
                [
                    'foreignKey' => 'id',
                    'joinType' => 'INNER'
                ],
            ],
            'wrongAssociationJoinType' => [
                false,
                'belongsTo',
                [
                    'foreignKey' => 'id',
                    'joinType' => 'LEFT'
                ],
            ],
            'wrongAssociationForeignKey' => [
                false,
                'belongsTo',
                [
                    'foreignKey' => 'fake_mammal_id',
                    'joinType' => 'INNER'
                ],
            ],
            'wrongAssociationClassName' => [
                false,
                'belongsTo',
                [
                    'foreignKey' => 'id',
                    'className' => 'Cake\ORM\Table'
                ],
            ],
        ];
    }

    /**
     * Test AddTableAlreadyAssociated
     *
     * @return void
     *
     * @dataProvider addTableAlreadyAssociatedProvider
     * @covers ::addTable()
     * @covers ::checkAssociation()
     */
    public function testAddTableAlreadyAssociated($expected, $associationType, $conf)
    {
        $cti = $this->fakeFelines->behaviors()->get('ClassTableInheritance');
        $cti->removeTable('FakeMammals');

        if ($expected === false) {
            $this->setExpectedException('\RuntimeException');
        }

        $this->fakeFelines->{$associationType}('FakeMammals', $conf);

        $this->assertEquals($cti, $cti->addTable('FakeMammals'));
        $ctiConf = $cti->config('table');

        $this->assertEquals(sort($expected), sort($ctiConf));

        $cti->removeTable('FakeMammals');
        $this->assertEquals(null, $cti->config('table'));
        $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->fakeFelines->association('FakeMammals'));
    }

    /**
     * Test inherited tables
     *
     * @return void
     * @covers ::inheritedTables()
     * @covers ::isTableInherited()
     */
    public function testInheritedTables()
    {
        $mammalsInheritance = $this->fakeMammals->inheritedTables();
        $this->assertEquals(['FakeAnimals'], array_column($mammalsInheritance, 'name'));

        $felinesInheritance = $this->fakeFelines->inheritedTables();
        $this->assertEquals(['FakeMammals'], array_column($felinesInheritance, 'name'));

        $felinesDeepInheritance = $this->fakeFelines->inheritedTables(true);
        $this->assertEquals(['FakeMammals', 'FakeAnimals'], array_column($felinesDeepInheritance, 'name'));

        $this->assertTrue($this->fakeFelines->isTableInherited('FakeAnimals', true));
        $this->assertFalse($this->fakeFelines->isTableInherited('FakeAnimals'));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals', true));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals'));
    }

    /**
     * Data provider for `testBuildContainString` test case.
     *
     * @return array
     */
    public function containStringProvider()
    {
        return [
            // expected, start value
            ['FakeMammals', 'FakeMammals'],
            ['FakeMammals.FakeAnimals', 'FakeAnimals'],
            ['FakeMammals.FakeAnimals.FakeArticles', 'FakeArticles'],
            [false, 'WrongAssociation']
        ];
    }

    /**
     * Test build contain string
     *
     * @return void
     *
     * @dataProvider containStringProvider
     * @covers ::buildContainString()
     */
    public function testBuildContainString($expected, $string)
    {
        $containString = $this->fakeFelines->buildContainString($string);
        $this->assertEquals($expected, $containString);
    }

    /**
     * Data provider for `testArrangeContain` test case.
     *
     * @return array
     */
    public function arrangeContainProvider()
    {
        return [
            'empty' => [
                [
                    'FakeMammals' => [
                        'FakeAnimals' => []
                    ]
                ],
                []
            ],
            'nestedAssociation' => [
                [
                    'FakeMammals' => [
                        'FakeAnimals' => [
                            'FakeArticles' => []
                        ]
                    ]
                ],
                ['FakeArticles']
            ]
        ];
    }

    /**
     * Test arrange contain
     *
     * @return void
     *
     * @dataProvider arrangeContainProvider
     * @covers ::arrangeContain()
     */
    public function testArrangeContain($expected, $contain)
    {
        $query = $this->fakeFelines->find()->contain($contain);
        $this->fakeFelines->arrangeContain($query);
        $this->assertEquals($expected, $query->contain());
    }

    /**
     * Test basic find
     *
     * @return void
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
        $this->assertEquals(ksort($expected), ksort($result));

        $this->assertFalse($feline->dirty());

        // hydrate false
        $felines = $this->fakeFelines->find()->hydrate(false);
        $this->assertEquals(1, $felines->count());

        $result = $felines->first();
        $this->assertEquals(ksort($expected), ksort($result));

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
     * @covers ::arrangeContain()
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
            'family' => 'purring cats'
        ];
    }
}
