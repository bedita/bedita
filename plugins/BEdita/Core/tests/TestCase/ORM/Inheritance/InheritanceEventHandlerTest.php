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
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\InheritanceEventHandler} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\InheritanceEventHandler
 */
class InheritanceEventHandlerTest extends TestCase
{
    use FakeAnimalsTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setupTables();
        $this->setupAssociations();
    }

    /**
     * Test implemented events.
     *
     * @return void
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        $expected = ['Model.beforeSave', 'Model.afterSave', 'Model.afterDelete', 'Model.afterRules'];

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
                    'name' => 'lion',
                ],
                [
                    'name' => 'lion',
                ],
            ],
            'no ancestors field' => [
                [
                    'id' => 4,
                    'family' => 'big cats',
                ],
                [
                    'family' => 'big cats',
                ],
            ],
            'no parent field' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'family' => 'big cats',
                ],
                [
                    'name' => 'tiger',
                    'family' => 'big cats',
                ],
            ],
            'simple' => [
                [
                    'id' => 4,
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                ],
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
                            'fake_animal_id' => 4,
                        ],
                        [
                            'id' => 2,
                            'title' => 'Puss in boots',
                            'body' => 'text',
                            'fake_animal_id' => 4,
                        ],
                    ],
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        '_ids' => [1, 2],
                    ],
                ],
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
                            'fake_animal_id' => 4,
                        ],
                        [
                            'id' => 4,
                            'title' => 'Sandokan',
                            'body' => 'The Malaysian tiger',
                            'fake_animal_id' => 4,
                        ],
                    ],
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                    'family' => 'big cats',
                    'fake_articles' => [
                        [
                            'title' => 'The white tiger',
                            'body' => 'Body of article',
                        ],
                        [
                            'title' => 'Sandokan',
                            'body' => 'The Malaysian tiger',
                        ],
                    ],
                ],
            ],
            'simple patch' => [
                [
                    'id' => 1,
                    'name' => 'The super cat',
                    'family' => 'purring cats',
                    'legs' => 4,
                    'subclass' => 'None',
                    'updated_at' => new Time('2018-02-20 09:50:00'),
                ],
                [
                    'id' => 1,
                    'name' => 'The super cat',
                    'subclass' => 'None',
                ],
            ],
        ];
    }

    /**
     * Test `beforeSave` event handler.
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @return void
     * @dataProvider saveProvider()
     * @covers ::beforeSave()
     * @covers ::toParent()
     * @covers ::toDescendant()
     */
    public function testBeforeSave($expected, $data)
    {
        $feline = $this->fakeFelines->newEntity([]);
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

            /** @var \Cake\Database\Connection $connection */
            $connection = $this->fakeFelines->getConnection();
            static::assertTrue($connection->inTransaction());

            return false; // This table is not meant to store data of your pet!
        });

        $feline = $this->fakeFelines->newEntity([]);
        $feline = $this->fakeFelines->patchEntity($feline, $data);
        $result = $this->fakeFelines->save($feline);

        static::assertFalse($result);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');

        static::assertSame($expectedFelines, $this->fakeFelines->find()->count());
        static::assertSame($expectedMammals, $this->fakeMammals->find()->count());
        static::assertSame($expectedAnimals, $this->fakeAnimals->find()->count());
    }

    /**
     * Data provider for `testApplicationRulesErrorsPropagation`.
     *
     * @return array
     */
    public function applicationRulesErrorsPropagationProvider()
    {
        $fakeFelinesError = [
            'family' => ['FakeFelinesFailure' => 'Invalid family.'],
        ];
        $fakeFelinesRule = [
            'table' => 'FakeFelines',
            'options' => [
                'errorField' => 'family',
                'message' => 'Invalid family.',
            ],
        ];

        $fakeMammalsError = [
            'subclass' => ['FakeMammalsFailure' => 'Invalid subclass.'],
        ];
        $fakeMammalsRule = [
            'table' => 'FakeMammals',
            'options' => [
                'errorField' => 'subclass',
                'message' => 'Invalid subclass.',
            ],
        ];

        $fakeAnimalsError = [
            'name' => ['FakeAnimalsFailure' => 'Invalid name.'],
        ];
        $fakeAnimalsRule = [
            'table' => 'FakeAnimals',
            'options' => [
                'errorField' => 'name',
                'message' => 'Invalid name.',
            ],
        ];

        return [
            'descendantError' => [
                $fakeFelinesError, // expected
                [$fakeFelinesRule], // rule config
            ],
            'middleError' => [
                $fakeMammalsError,
                [$fakeMammalsRule],
            ],
            'ancestorError' => [
                $fakeAnimalsError,
                [$fakeAnimalsRule],
            ],
            'ancestorsErrors' => [
                array_merge($fakeMammalsError, $fakeAnimalsError),
                [$fakeMammalsRule, $fakeAnimalsRule],
            ],
            'allInheritanceErrors' => [
                array_merge($fakeFelinesError, $fakeMammalsError, $fakeAnimalsError),
                [$fakeFelinesRule, $fakeMammalsRule, $fakeAnimalsRule],
            ],
            'firstAndLastInheritanceErrors' => [
                array_merge($fakeFelinesError, $fakeAnimalsError),
                [$fakeFelinesRule, $fakeAnimalsRule],
            ],
        ];
    }

    /**
     * Test that when some application rule fails
     * the errors are propagated to last descendant.
     *
     * @param array $expected The expected rule errors.
     * @param array $rulesConfig The rules configuration.
     * @return void
     * @dataProvider applicationRulesErrorsPropagationProvider
     * @covers ::beforeSave()
     * @covers ::afterRules()
     */
    public function testApplicationRulesErrorsPropagation($expected, $rulesConfig)
    {
        foreach ($rulesConfig as $rule) {
            $table = TableRegistry::getTableLocator()->get($rule['table']);
            $options = $rule['options'];
            $table->getEventManager()->on(
                'Model.buildRules',
                function (Event $event, RulesChecker $rules) use ($table, $options) {
                    $rules->add(
                        function () {
                            return false;
                        },
                        sprintf('%sFailure', $table->getAlias()),
                        $options
                    );
                }
            );
        }

        $data = [
            'name' => 'Gustavo',
            'legs' => 2,
            'subclass' => 'Unknown',
            'family' => 'Supporters',
        ];

        $feline = $this->fakeFelines->newEntity($data);
        $result = $this->fakeFelines->save($feline);
        static::assertFalse($result);
        static::assertEquals($expected, $feline->getErrors());
    }

    /**
     * Test options passed in che chain
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveOptions()
    {
        $eventDispatchedFelines = $eventDispatchedMammals = $eventDispatchedAnimals = 0;
        // Main table
        $this->fakeFelines->getEventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) use (&$eventDispatchedFelines) {
                $eventDispatchedFelines++;
                static::assertArrayNotHasKey('_inherited', $options);
                static::assertTrue($options['atomic']);
            }
        );

        // Inherited table
        $this->fakeMammals->getEventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) use (&$eventDispatchedMammals) {
                $eventDispatchedMammals++;
                static::assertArrayHasKey('_inherited', $options);
                static::assertTrue($options['_inherited']);
                static::assertFalse($options['atomic']);
            }
        );

        // Inherited table
        $this->fakeAnimals->getEventManager()->on(
            'Model.beforeSave',
            function (Event $event, EntityInterface $entity, \ArrayObject $options) use (&$eventDispatchedAnimals) {
                $eventDispatchedAnimals++;
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

        static::assertSame(1, $eventDispatchedFelines);
        static::assertSame(1, $eventDispatchedMammals);
        static::assertSame(1, $eventDispatchedAnimals);
    }

    /**
     * Test `afterDelete` event handler.
     *
     * @return void
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

            /** @var \Cake\Database\Connection $connection */
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
