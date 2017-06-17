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

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\AssociationCollection;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * {@see \BEdita\Core\ORM\Inheritance\AssociationCollection} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\AssociationCollection
 */
class AssociationCollectionTest extends TestCase
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
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeAnimals->hasMany('FakeArticles', ['dependent' => true]);

        $this->fakeMammals = TableRegistry::get('FakeMammals', ['className' => Table::class]);
        $this->fakeMammals->hasMany('FakeFelines', [
            'className' => Table::class,
            'foreignKey' => 'id',
        ]);
    }

    /**
     * Test constructor.
     *
     * @return void
     *
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $association = $this->fakeMammals->association('FakeFelines');
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());

        static::assertAttributeSame($this->fakeMammals, 'table', $collection);
        static::assertAttributeSame($this->fakeAnimals->associations(), 'innerCollection', $collection);

        static::assertAttributeContains($association, '_items', $collection);
    }

    /**
     * Data provider for `testGetHas` and `testGetByProperty` test cases.
     *
     * @return array
     */
    public function getProvider()
    {
        return [
            'own' => [
                'FakeFelines',
                'FakeFelines',
            ],
            'parent' => [
                'FakeArticles',
                'FakeArticles',
            ],
            'not found' => [
                null,
                'GustavoSupporto',
            ],
        ];
    }

    /**
     * Test association getter.
     *
     * @param string|null $expected Expected alias, or `null`.
     * @param string $alias Alias to search for.
     * @return void
     *
     * @dataProvider getProvider()
     * @covers ::get()
     * @covers ::has()
     * @covers ::inheritAssociation()
     */
    public function testGetHas($expected, $alias)
    {
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());

        $association = $collection->get($alias);
        $exists = $collection->has($alias);
        if ($expected === null) {
            static::assertFalse($exists);
            static::assertNull($association);
        } else {
            static::assertTrue($exists);
            static::assertInstanceOf(Association::class, $association);
            static::assertSame($expected, $association->getAlias());
            static::assertSame($this->fakeMammals, $association->getSource());
        }
    }

    /**
     * Test association getter.
     *
     * @param string|null $expected Expected alias, or `null`.
     * @param string $alias Property to search for.
     * @return void
     *
     * @dataProvider getProvider()
     * @covers ::getByProperty()
     * @covers ::inheritAssociation()
     */
    public function testGetByProperty($expected, $alias)
    {
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());

        $association = $collection->getByProperty(Inflector::underscore($alias));
        if ($expected === null) {
            static::assertNull($association);
        } else {
            static::assertInstanceOf(Association::class, $association);
            static::assertSame($expected, $association->getAlias());
            static::assertSame($this->fakeMammals, $association->getSource());
        }
    }

    /**
     * Test getter of association keys.
     *
     * @return void
     *
     * @covers ::keys()
     */
    public function testKeys()
    {
        $expected = ['fakefelines', 'fakearticles'];

        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $keys = $collection->keys();

        static::assertSame($expected, $keys);
    }

    /**
     * Test getter of associations by type.
     *
     * @return void
     *
     * @covers ::type()
     */
    public function testType()
    {
        $expected = ['FakeFelines', 'FakeArticles'];

        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $aliases = array_map(
            function (Association $association) {
                return $association->getAlias();
            },
            $collection->type('HasMany')
        );

        static::assertSame($expected, $aliases);
    }

    /**
     * Test removal of association.
     *
     * @return void
     *
     * @covers ::remove()
     */
    public function testRemove()
    {
        $association = $this->fakeMammals->association('FakeFelines');
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $collection->remove('FakeFelines');

        static::assertAttributeNotContains($association, '_items', $collection);
    }

    /**
     * Test removal of inherited association.
     *
     * @return void
     *
     * @covers ::remove()
     */
    public function testRemoveInner()
    {
        $association = $this->fakeAnimals->association('FakeArticles');
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $collection->remove('FakeArticles');

        static::assertAttributeNotContains($association, '_items', $this->fakeAnimals->associations());
    }

    /**
     * Test removal of association without cascading.
     *
     * @return void
     *
     * @covers ::remove()
     */
    public function testRemoveNoCascade()
    {
        $association = $this->fakeAnimals->association('FakeArticles');
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $collection->remove('FakeArticles', false);

        static::assertAttributeContains($association, '_items', $this->fakeAnimals->associations());
    }

    /**
     * Test removal of all associations.
     *
     * @return void
     *
     * @covers ::removeAll()
     */
    public function testRemoveAll()
    {
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $collection->removeAll();

        static::assertAttributeSame([], '_items', $collection);
        static::assertAttributeSame([], '_items', $this->fakeAnimals->associations());
    }

    /**
     * Test cascading deletes to all associations with proper handling of callbacks.
     *
     * @return void
     *
     * @covers ::_getNoCascadeItems()
     */
    public function testCascadeDelete()
    {
        $this->fakeAnimals->association('FakeArticles')->eventManager()->on('Model.beforeDelete', function () {
            static::fail('Callbacks triggered');
        });

        $mammal = $this->fakeMammals->get(1);
        $collection = new AssociationCollection($this->fakeMammals, $this->fakeAnimals->associations());
        $collection->cascadeDelete($mammal, []);

        static::assertSame(0, $this->fakeAnimals->association('FakeArticles')->find()->where(['fake_animal_id' => 1])->count());
    }
}
