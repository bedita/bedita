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

namespace BEdita\Core\Test\TestCase\ORM\Association;

use BEdita\Core\ORM\Association\ExtensionOf;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Association\ExtensionOf} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Association\ExtensionOf
 */
class ExtensionOfTest extends TestCase
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
        $this->fakeMammals = TableRegistry::get('FakeMammals');
        $this->fakeFelines = TableRegistry::get('FakeFelines');

        $this->fakeAnimals->hasMany('FakeArticles', [
            'dependent' => true
        ]);
    }

    /**
     * Test __constructor to see if Model.afterDelete is set
     *
     * @return void
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $count = count($this->fakeMammals->eventManager()->listeners('Model.afterDelete'));

        new ExtensionOf('FakeAnimals', [
            'sourceTable' => $this->fakeMammals,
            'foreignKey' => $this->fakeMammals->getPrimaryKey()
        ]);

        $count++;
        $this->assertCount($count, $this->fakeMammals->eventManager()->listeners('Model.afterDelete'));
    }

    /**
     * Test testNewAssociation
     *
     * @return void
     * @covers ::type()
     */
    public function testNewAssociation()
    {
        $assoc = new ExtensionOf('FakeAnimals', [
            'sourceTable' => $this->fakeMammals,
            'foreignKey' => $this->fakeMammals->getPrimaryKey()
        ]);

        $this->assertEquals(Association::ONE_TO_ONE, $assoc->type());
        $this->assertEquals('INNER', $assoc->getJoinType());
        $this->assertFalse($assoc->getDependent());
        $this->assertFalse($assoc->getCascadeCallbacks());
        $this->assertEquals($this->fakeAnimals->getPrimaryKey(), $assoc->getBindingKey());
        $this->assertEquals('fake_animal', $assoc->getProperty());
        $this->assertFalse($assoc->isOwningSide($this->fakeMammals));
        $this->assertTrue($assoc->isOwningSide($this->fakeAnimals));
    }

    /**
     * Data provider for `testSaveAssociated` test case.
     *
     * @return array
     */
    public function saveAssociatedProvider()
    {
        return [
            'noParentField' => [
                [
                    'subclass' => 'Eutheria'
                ]
            ],
            'parentField' => [
                [
                    'subclass' => 'Marsupial',
                    'name' => 'kangaroo',
                    'legs' => 4
                ]
            ],
        ];
    }

    /**
     * Test testSaveAssociated
     *
     * @param array $entityData Entity data.
     * @return void
     * @dataProvider saveAssociatedProvider
     * @covers ::saveAssociated()
     * @covers ::targetPropertiesValues()
     */
    public function testSaveAssociated($entityData)
    {
        $assoc = new ExtensionOf('FakeAnimals', [
            'sourceTable' => $this->fakeMammals,
            'foreignKey' => $this->fakeMammals->getPrimaryKey()
        ]);

        $this->fakeMammals->associations()->add($assoc->getName(), $assoc);
        $mammal = $this->fakeMammals->newEntity($entityData);

        $lastInserted = $this->fakeAnimals
            ->find()
            ->enableHydration(false)
            ->last();

        $expectedId = $lastInserted['id'] + 1;

        $mammal = $assoc->saveAssociated($mammal);
        $this->assertEquals($expectedId, $mammal->id);
        $this->assertEquals($expectedId, $mammal->id);
    }

    /**
     * Test testDependent
     *
     * @return void
     * @coversNothing
     */
    public function testDependent()
    {
        $assoc = new ExtensionOf('FakeAnimals', [
            'sourceTable' => $this->fakeMammals,
            'foreignKey' => $this->fakeMammals->getPrimaryKey()
        ]);
        $this->fakeMammals->associations()->add($assoc->getName(), $assoc);

        $assoc = new ExtensionOf('FakeMammals', [
            'sourceTable' => $this->fakeFelines,
            'foreignKey' => $this->fakeFelines->getPrimaryKey()
        ]);
        $this->fakeFelines->associations()->add($assoc->getName(), $assoc);

        $feline = $this->fakeFelines->find()
            ->contain('FakeMammals.FakeAnimals')
            ->last();

        $id = $feline->id;
        $this->fakeFelines->delete($feline);
        foreach (['fakeFelines', 'fakeMammals', 'fakeAnimals'] as $table) {
            try {
                $this->{$table}->get($id);
                $this->fail(ucfirst($table) . ' record not deleted');
            } catch (RecordNotFoundException $ex) {
                continue;
            }
        }

        $articles = $this->fakeAnimals
            ->FakeArticles
            ->find()
            ->where(['fake_animal_id' => $id])
            ->count();

        $this->assertEquals(0, $articles);
    }
}
