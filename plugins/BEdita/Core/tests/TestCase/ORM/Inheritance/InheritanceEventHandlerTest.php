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

use BEdita\Core\ORM\Inheritance\InheritanceEventHandler;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\InheritanceEventHandler} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\InheritanceEventHandler
 */
class InheritanceEventHandlerTest extends TestCase
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
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeFelines = TableRegistry::get('FakeFelines', ['className' => Table::class]);
        $this->fakeMammals = TableRegistry::get('FakeMammals', ['className' => Table::class]);
        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeMammals->extensionOf('FakeAnimals');
        $this->fakeFelines->extensionOf('FakeMammals');
        $this->fakeAnimals->hasMany('FakeArticles', ['dependent' => true]);
    }

    /**
     * Test implemented events.
     *
     * @return void
     *
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        $expected = ['Model.beforeSave', 'Model.afterDelete'];

        $handler = new InheritanceEventHandler();
        $implementedEvents = array_keys($handler->implementedEvents());

        static::assertSame($expected, $implementedEvents);
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'only ancestor field' => [
                [
                    'id' => 4,
                    'name' => 'lion'
                ],
                [
                    'name' => 'lion'
                ]
            ],
            'no ancestors field' => [
                [
                    'id' => 4,
                    'family' => 'big cats'
                ],
                [
                    'family' => 'big cats'
                ]
            ],
            'no parent field' => [
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
            'advanced new articles' => [
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
            'simple patch' => [
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
     * Test `beforeSave` event handler.
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider saveProvider()
     * @covers ::beforeSave()
     * @covers ::toParent()
     * @covers ::toDescendant()
     */
    public function testBeforeSave($expected, $data)
    {
        $feline = $this->fakeFelines->newEntity();
        if (!empty($data['id'])) {
            $feline = $this->fakeFelines->get($data['id']);
        }
        $feline = $this->fakeFelines->patchEntity($feline, $data);
        $result = $this->fakeFelines->save($feline);

        static::assertNotFalse($result);
        $resultArray = $result->toArray();
        static::assertEquals($expected, $resultArray);

        static::assertSame(1, $this->fakeFelines->find()->where(['id' => $result->id])->count());
        static::assertSame(1, $this->fakeMammals->find()->where(['id' => $result->id])->count());
        static::assertSame(1, $this->fakeAnimals->find()->where(['id' => $result->id])->count());
    }

    /**
     * Test rollback if save on a parent table fails.
     *
     * @return void
     *
     * @covers ::beforeSave()
     * @covers ::toParent()
     * @covers ::toDescendant()
     */
    public function testBeforeSaveFailure()
    {
        $data = [
            'name' => 'Cleopatra', // She's my cat. <3
            'legs' => 4,
            'subclass' => 'Sleepy pets',
            'family' => '@fquffio\'s family',
        ];
        $expectedAnimals = $this->fakeAnimals->find()->count();
        $expectedMammals = $this->fakeMammals->find()->count();
        $expectedFelines = $this->fakeFelines->find()->count();

        $eventDispatched = 0;
        $this->fakeAnimals->getEventManager()->on('Model.beforeSave', function () use (&$eventDispatched) {
            $eventDispatched++;

            /* @var \Cake\Database\Connection $connection */
            $connection = $this->fakeFelines->getConnection();
            static::assertTrue($connection->inTransaction());

            return false; // This table is not meant to store data of your pet!
        });

        $feline = $this->fakeFelines->newEntity();
        $feline = $this->fakeFelines->patchEntity($feline, $data);
        $result = $this->fakeFelines->save($feline);

        static::assertFalse($result);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');

        static::assertSame($expectedFelines, $this->fakeFelines->find()->count());
        static::assertSame($expectedMammals, $this->fakeMammals->find()->count());
        static::assertSame($expectedAnimals, $this->fakeAnimals->find()->count());
    }

    /**
     * Test options passed in che chain
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveOptions()
    {
        // Main table
        $this->fakeFelines->eventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) {
                static::assertArrayNotHasKey('_inherited', $options);
                static::assertTrue($options['atomic']);
            }
        );

        // Inherited table
        $this->fakeMammals->eventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) {
                static::assertArrayHasKey('_inherited', $options);
                static::assertTrue($options['_inherited']);
                static::assertFalse($options['atomic']);
            }
        );

        // Inherited table
        $this->fakeAnimals->eventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) {
                static::assertArrayHasKey('_inherited', $options);
                static::assertTrue($options['_inherited']);
                static::assertFalse($options['atomic']);
            }
        );

        $feline = $this->fakeFelines->newEntity([
            'name' => 'Gastone',
            'legs' => 4,
            'subclass' => 'Lucky pets',
            'family' => 'Cats',
        ]);
        $result = $this->fakeFelines->save($feline);
    }

    /**
     * Test `afterDelete` event handler.
     *
     * @return void
     *
     * @covers ::afterDelete()
     * @covers ::toParent()
     */
    public function testAfterDelete()
    {
        $feline = $this->fakeFelines->get(1);
        $result = $this->fakeFelines->delete($feline);

        static::assertNotFalse($result);

        static::assertSame(0, $this->fakeFelines->find()->where(['id' => 1])->count());
        static::assertSame(0, $this->fakeMammals->find()->where(['id' => 1])->count());
        static::assertSame(0, $this->fakeAnimals->find()->where(['id' => 1])->count());
    }

    /**
     * Test rollback if delete on a parent table fails.
     *
     * @return void
     *
     * @covers ::afterDelete()
     * @covers ::toParent()
     */
    public function testAfterDeleteFailure()
    {
        $expectedAnimals = $this->fakeAnimals->find()->count();
        $expectedMammals = $this->fakeMammals->find()->count();
        $expectedFelines = $this->fakeFelines->find()->count();

        $eventDispatched = 0;
        $this->fakeAnimals->getEventManager()->on('Model.beforeDelete', function () use (&$eventDispatched) {
            $eventDispatched++;

            /* @var \Cake\Database\Connection $connection */
            $connection = $this->fakeFelines->getConnection();
            static::assertTrue($connection->inTransaction());

            return false;
        });

        $feline = $this->fakeFelines->get(1);
        $exception = null;
        try {
            $this->fakeFelines->delete($feline);
        } catch (PersistenceFailedException $exception) {
            // Nothing to do.
        }

        static::assertInstanceOf(PersistenceFailedException::class, $exception, 'Exception not raised');
        static::assertSame(1, $eventDispatched, 'Event not dispatched');

        static::assertSame($expectedFelines, $this->fakeFelines->find()->count());
        static::assertSame($expectedMammals, $this->fakeMammals->find()->count());
        static::assertSame($expectedAnimals, $this->fakeAnimals->find()->count());
    }
}
