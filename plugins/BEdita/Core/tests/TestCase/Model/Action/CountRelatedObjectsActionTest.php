<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\CountRelatedObjectsAction;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * {@see \BEdita\Core\Model\Action\CountRelatedObjectsAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\CountRelatedObjectsAction
 */
class CountRelatedObjectsActionTest extends TestCase
{
    use LocatorAwareTrait;

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * RelationsTable instance
     *
     * @var \BEdita\Core\Model\Table\RelationsTable
     */
    protected $Relations = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Relations = $this->getTableLocator()->get('Relations');
    }

    /**
     * Data provider for testExecute()
     *
     * @return array
     */
    public function executeProvider(): array
    {
        return [
            'no count' => [
                [],
                [],
                'hello_there',
            ],
            'count test on documents' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                ],
                [],
                'test',
            ],
            'skip not valid relation' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                ],
                [],
                'test,hello_there',
            ],
            'count more relation' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                            'inverse_test' => 1,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                ],
                [],
                'test,inverse_test',
            ],
            'count more relation passed as array' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                            'inverse_test' => 1,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                        ],
                    ],
                ],
                [],
                ['test', 'inverse_test'],
            ],
            'count relation inside object related too' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                        ],
                    ],
                    [
                        'id' => 4,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                        ],
                    ],
                ],
                ['Test'],
                'test',
            ],
            'count all relation inside object related too' => [
                [
                    [
                        'id' => 2,
                        'count' => [
                            'test' => 2,
                            'inverse_test' => 0,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                    [
                        'id' => 3,
                        'count' => [
                            'test' => 1,
                            'inverse_test' => 1,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                    [
                        'id' => 4,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 2,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                    [
                        'id' => 6,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                    [
                        'id' => 7,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                    [
                        'id' => 15,
                        'count' => [
                            'test' => 0,
                            'inverse_test' => 0,
                            'another_test' => 0,
                            'inverse_another_test' => 0,
                            'test_abstract' => 0,
                            'inverse_test_abstract' => 0,
                            'placeholder' => 0,
                            'placeholded' => 0,
                        ],
                    ],
                ],
                ['Test'],
                'all',
            ],
        ];
    }

    /**
     * Test execute action.
     *
     * @param array $expected The expected result
     * @param array $contain The contain option
     * @param mixed $count Relations to count
     * @return void
     *
     * @covers ::execute()
     * @covers ::extractIds()
     * @covers ::getRelationsList()
     * @covers ::filterCount()
     * @covers ::countRelations()
     * @covers ::hydrateCount()
     * @covers ::groupResultCountById()
     * @covers ::searchEntitiesById()
     * @covers ::searchEntitiesInProperties()
     * @dataProvider executeProvider
     */
    public function testExecute(array $expected, array $contain, $count): void
    {
        $Documents = $this->getTableLocator()->get('Documents');
        $entities = $Documents->find('type', ['documents'])
            ->contain($contain)
            ->toArray();

        $action = new CountRelatedObjectsAction();
        $result = $action(compact('entities', 'count'));

        $expected = Hash::combine($expected, '{n}.id', '{n}');
        $result = Hash::combine($result, '{n}.id', '{n}');

        static::assertEquals($expected, $result);

        if (empty($result)) {
            return;
        }

        $props = [];
        if (!empty($contain)) {
            $props = array_map(function ($item) {
                return Inflector::underscore($item);
            }, $contain);
        }

        foreach ($entities as $entity) {
            static::assertTrue($entity->has('_countData'));
            static::assertEquals(Hash::get($result[$entity->id], 'count'), $entity->get('_countData'));

            if (empty($props)) {
                continue;
            }

            $related = $entity->extract($props);
            collection($related)
                ->unfold()
                ->each(function ($r) use ($result) {
                    static::assertTrue($r->has('_countData'));
                    static::assertEquals(Hash::get($result[$r->id], 'count'), $r->get('_countData'));
                });
        }
    }

    /**
     * Test that trying to count relations on entities that aren't ObjectEntity
     * it will return an empty array
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::extractIds()
     */
    public function testNotObjectEntity(): void
    {
        $roles = $this->getTableLocator()->get('Roles')->find()->toArray();

        $action = new CountRelatedObjectsAction();
        $result = $action(['entities' => $roles, 'count' => 'test']);

        static::assertEquals([], $result);
    }

    /**
     * Test that passing empty count will return empty array.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::filterCount()
     */
    public function testEmptyCount(): void
    {
        $action = new CountRelatedObjectsAction();
        $result = $action(['count' => '']);

        static::assertEquals([], $result);
    }

    /**
     * Test that setting `hydrate` to false the entity will be untouched.
     *
     * @return void
     *
     * @covers ::execute()
     */
    public function testNoHydrate(): void
    {
        $Documents = $this->getTableLocator()->get('Documents');
        $entities = $Documents->find('type', ['documents'])->toArray();
        $count = 'test';

        $action = new CountRelatedObjectsAction(['hydrate' => false]);
        $result = $action(compact('entities', 'count'));

        static::assertNotEmpty($result);
        foreach ($entities as $entity) {
            static::assertFalse($entity->has('_countData'));
        }
    }

    /**
     * Test that the entities hydration was skipped if missing `id` or `count`.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::hydrateCount()
     */
    public function testHydrateCountNotValidCountData(): void
    {
        $fakeResult = [
            [
                'id' => 2,
                'count' => [
                    'test' => 2,
                ],
            ],
            [
                'id' => 3,
            ],
            [
                'count' => 4,
            ],
        ];

        $mock = $this->getMockBuilder(CountRelatedObjectsAction::class)
            ->setMethods(['groupResultCountById'])
            ->getMock();

        $mock->method('groupResultCountById')
            ->willReturn($fakeResult);

        $Documents = $this->getTableLocator()->get('Documents');
        $entities = [$Documents->get(2)];
        $count = 'test';

        $result = $mock(compact('entities', 'count'));
        static::assertEquals($fakeResult, $result);

        static::assertTrue($entities[0]->has('_countData'));
    }

    /**
     * Test that entity count won't be hydrated if id of count data is not found.
     *
     * @return void
     *
     * @covers ::hydrateCount()
     * @covers ::searchEntitiesById()
     */
    public function testHydrateCountNotFoundObject(): void
    {
        $fakeResult = [
            [
                'id' => 1000000,
                'count' => [
                    'test' => 2,
                ],
            ],
        ];

        $mock = $this->getMockBuilder(CountRelatedObjectsAction::class)
            ->setMethods(['groupResultCountById'])
            ->getMock();

        $mock->method('groupResultCountById')
            ->willReturn($fakeResult);

        $Documents = $this->getTableLocator()->get('Documents');
        $entities = [$Documents->get(2)];
        $count = 'test';

        $mock(compact('entities', 'count'));

        static::assertFalse($entities[0]->has('_countData'));
    }
}
