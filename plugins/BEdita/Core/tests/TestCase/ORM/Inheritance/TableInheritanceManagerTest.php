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

use BEdita\Core\ORM\Association\ExtensionOf;
use BEdita\Core\ORM\Inheritance\TableInheritanceManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\TableInheritanceManager} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\TableInheritanceManager
 */
class TableInheritanceManagerTest extends TestCase
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
    ];

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
        $this->fakeFelines = TableRegistry::get('FakeFelines');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        TableRegistry::clear();
        unset($this->fakeFelines);
        parent::tearDown();
    }

    /**
     * Data provider for `testAddTable` test case.
     *
     * @return array
     */
    public function addTableProvider()
    {
        return [
            'addTableOk' => [
                [
                    'tableName' => 'FakeMammals',
                    'instanceOf' => 'Cake\ORM\Table'
                ],
                [
                    'tableName' => 'FakeMammals'
                ]
            ],
            'missingTableName' => [
                'missingTableName',
                []
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
     * @covers ::createAssociation()
     */
    public function testAddTable($expected, $conf = [])
    {
        $this->assertCount(0, $this->fakeFelines->associations()->type('ExtensionOf'));

        if ($expected === 'missingTableName') {
            $this->setExpectedException('\InvalidArgumentException');
        }
        TableInheritanceManager::addTable($this->fakeFelines, $conf);
        $this->assertCount(1, $this->fakeFelines->associations()->type('ExtensionOf'));

        // trying to add again do nothing
        TableInheritanceManager::addTable($this->fakeFelines, $conf);
        $this->assertCount(1, $this->fakeFelines->associations()->type('ExtensionOf'));

        $inherited = TableInheritanceManager::inheritedTables($this->fakeFelines);
        $inherited = current($inherited);
        $this->assertInstanceOf($expected['instanceOf'], $inherited);
        $this->assertEquals($expected['tableName'], $inherited->alias());

        $association = $this->fakeFelines->association($inherited->alias());
        $this->assertInstanceOf('BEdita\Core\ORM\Association\ExtensionOf', $association);
        $this->assertEquals($this->fakeFelines->primaryKey(), $association->foreignKey());
        $this->assertEquals('INNER', $association->joinType());
        $this->assertTrue($association->dependent());
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
                    'tableName' => 'FakeMammals'
                ],
                'extensionOf',
                [
                    'tableName' => 'FakeMammals',
                    'className' => 'Cake\ORM\Table'
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
                'extensionOf',
                [
                    'createFirst' => true,
                    'joinType' => 'LEFT'
                ],
            ],
            'wrongAssociationForeignKey' => [
                false,
                'extensionOf',
                [
                    'createFirst' => true,
                    'foreignKey' => 'fake_mammal_id'
                ],
            ],
            'wrongAssociationClassName' => [
                false,
                'extensionOf',
                [
                    'tableName' => 'FakeMammals',
                    'className' => 'BEdita\Core\Model\Table\ObjectsTable'
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
    public function testAddAlreadyAssociatedTable($expected, $associationType, $conf)
    {
        if ($expected === false) {
            $this->setExpectedException('\RuntimeException');
        }

        if ($associationType != 'extensionOf') {
            $this->fakeFelines->{$associationType}('FakeMammals', $conf);
            TableInheritanceManager::addTable($this->fakeFelines, ['tableName' => 'FakeMammals']);
        } elseif (!empty($conf['createFirst'])) {
            $conf['sourceTable'] = $this->fakeFelines;
            $association = new ExtensionOf('FakeMammals', $conf);
            $this->fakeFelines->associations()->add($association->name(), $association);
            TableInheritanceManager::addTable($this->fakeFelines, ['tableName' => 'FakeMammals']);
        } else {
            TableInheritanceManager::addTable($this->fakeFelines, $conf);
            TableInheritanceManager::addTable($this->fakeFelines, ['tableName' => 'FakeMammals', 'className' => 'Cake\ORM\Table']);
        }
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
                'FakeTable',
            ],
            'nestedInherited' => [
                false,
                'FakeAnimals',
            ],
            'removeOk' => [
                [],
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
    public function testRemoveTable($expected, $tableName)
    {
        $fakeMammals = TableRegistry::get('FakeMammals');
        TableInheritanceManager::addTable($fakeMammals, ['tableName' => 'FakeAnimals']);
        TableInheritanceManager::addTable($this->fakeFelines, ['tableName' => 'FakeMammals']);
        $this->assertCount(1, $this->fakeFelines->associations()->type('ExtensionOf'));

        if ($expected === false) {
            $this->setExpectedException('\RuntimeException');
        }

        TableInheritanceManager::removeTable($this->fakeFelines, $tableName);

        $this->assertEquals($expected, TableInheritanceManager::inheritedTables($this->fakeFelines));
        $this->assertCount(0, $this->fakeFelines->associations()->type('ExtensionOf'));
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
        $fakeMammals = TableRegistry::get('FakeMammals');
        TableInheritanceManager::addTable($fakeMammals, ['tableName' => 'FakeAnimals']);
        TableInheritanceManager::addTable($this->fakeFelines, ['tableName' => 'FakeMammals']);

        $mammalsInheritance = current(TableInheritanceManager::inheritedTables($fakeMammals));

        $this->assertEquals('FakeAnimals', $mammalsInheritance->alias());

        $felinesInheritance = current(TableInheritanceManager::inheritedTables($this->fakeFelines));
        $this->assertEquals('FakeMammals', $felinesInheritance->alias());

        $felinesDeepInheritance = array_map(function ($inherited) {
            return $inherited->alias();
        }, TableInheritanceManager::inheritedTables($this->fakeFelines, true));

        $this->assertEquals(['FakeMammals', 'FakeAnimals'], $felinesDeepInheritance);

        $this->assertTrue(TableInheritanceManager::isTableInherited($this->fakeFelines, 'FakeAnimals', true));
        $this->assertFalse(TableInheritanceManager::isTableInherited($this->fakeFelines, 'FakeAnimals'));
        $this->assertTrue(TableInheritanceManager::isTableInherited($this->fakeFelines, 'FakeMammals', true));
        $this->assertTrue(TableInheritanceManager::isTableInherited($this->fakeFelines, 'FakeMammals'));
    }
}
