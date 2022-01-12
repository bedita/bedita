<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * Test validation on relationships params.
 */
class RelationshipsParamsTest extends IntegrationTestCase
{
    /**
     * Locations table.
     *
     * @var \BEdita\Core\Model\Table\LocationsTable
     */
    protected $Locations;

    /**
     * Users table.
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * Relations table.
     *
     * @var \BEdita\Core\Model\Table\RelationsTable
     */
    protected $Relations;

    /**
     * Object relations table.
     *
     * @var \BEdita\Core\Model\Table\ObjectRelationsTable
     */
    protected $ObjectRelations;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Locations = TableRegistry::getTableLocator()->get('Locations');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->Relations = TableRegistry::getTableLocator()->get('Relations');
        $this->ObjectRelations = TableRegistry::getTableLocator()->get('ObjectRelations');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Locations, $this->Users, $this->Relations, $this->ObjectRelations);

        parent::tearDown();
    }

    /**
     * Test success with valid parameters.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testOk()
    {
        $params = [
            'name' => 'Gustavo',
            'age' => 42,
        ];
        $relation = $this->Relations->find()
            ->where(['name' => 'another_test'])
            ->firstOrFail();

        // Load two locations.
        $firstUser = $this->Users->find()->first();
        $secondLocation = $this->Locations->newEntity(['title' => 'Another location']);
        $secondLocation->created_by = 1;
        $secondLocation->modified_by = 1;
        $secondLocation = $this->Locations->saveOrFail($secondLocation);

        $existing = $this->ObjectRelations->exists([
            'left_id' => $firstUser->id,
            'right_id' => $secondLocation->id,
            'relation_id' => $relation->id,
        ]);
        static::assertFalse($existing);

        $data = [
            [
                'id' => $secondLocation->id,
                'type' => 'locations',
                'meta' => [
                    'relation' => compact('params'),
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $endpoint = sprintf('/users/%d/relationships/%s', $firstUser->id, $relation->get('name'));
        $this->_sendRequest($endpoint, 'POST', json_encode(compact('data')));

        $this->assertResponseCode(200);

        $existing = $this->ObjectRelations->exists([
            'left_id' => $firstUser->id,
            'right_id' => $secondLocation->id,
            'relation_id' => $relation->id,
        ]);
        static::assertTrue($existing);

        $actual = $this->ObjectRelations->find()
            ->where([
                'left_id' => $firstUser->id,
                'right_id' => $secondLocation->id,
                'relation_id' => $relation->id,
            ])
            ->first()
            ->get('params');
        static::assertEquals($params, $actual);
    }

    /**
     * Data provider for `testInvalidParams` test case.
     *
     * @return array
     */
    public function invalidParamsProvider()
    {
        return [
            'POST, not an object' => [
                '[params.valid]: Object expected, "this is an invalid parameter data" received',
                'this is an invalid parameter data',
                'POST',
            ],
            'POST, missing required property' => [
                '[params.valid]: Required property missing: name',
                [
                    'age' => 42,
                ],
                'POST',
            ],
            'POST, violated constraints' => [
                '[params.valid]: String expected, true received',
                [
                    'name' => true,
                ],
                'POST',
            ],
            'PATCH, not an object' => [
                '[params.valid]: Object expected, "this is an invalid parameter data" received',
                'this is an invalid parameter data',
                'PATCH',
            ],
            'PATCH, missing required property' => [
                '[params.valid]: Required property missing: name',
                [
                    'age' => 42,
                ],
                'PATCH',
            ],
            'PATCH, violated constraints' => [
                '[params.valid]: String expected, true received',
                [
                    'name' => true,
                ],
                'PATCH',
            ],
        ];
    }

    /**
     * Test failure on invalid parameters.
     *
     * @param string $expected Expected error message.
     * @param mixed $params Relation parameters.
     * @param string $method Request method (either `POST` or `PATCH`).
     * @return void
     *
     * @dataProvider invalidParamsProvider()
     * @coversNothing
     */
    public function testInvalidParams($expected, $params, $method)
    {
        $relation = $this->Relations->find()
            ->where(['name' => 'another_test'])
            ->firstOrFail();

        // Load two locations.
        $firstUser = $this->Users->find()->first();
        $secondLocation = $this->Locations->newEntity(['title' => 'Another location']);
        $secondLocation->created_by = 1;
        $secondLocation->modified_by = 1;
        $secondLocation = $this->Locations->saveOrFail($secondLocation);

        $existing = $this->ObjectRelations->exists([
            'left_id' => $firstUser->id,
            'right_id' => $secondLocation->id,
            'relation_id' => $relation->id,
        ]);
        static::assertFalse($existing);

        $data = [
            [
                'id' => $secondLocation->id,
                'type' => 'locations',
                'meta' => [
                    'relation' => compact('params'),
                ],
            ],
        ];

        $this->configRequestHeaders($method, $this->getUserAuthHeader());
        $endpoint = sprintf('/users/%d/relationships/%s', $firstUser->id, $relation->get('name'));
        $this->_sendRequest($endpoint, $method, json_encode(compact('data')));

        $this->assertResponseCode(400);

        $existing = $this->ObjectRelations->exists([
            'left_id' => $firstUser->id,
            'right_id' => $secondLocation->id,
            'relation_id' => $relation->id,
        ]);
        static::assertFalse($existing);

        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('error', $body);
        static::assertArrayHasKey('detail', $body['error']);
        static::assertContains($expected, $body['error']['detail']);
    }

    /**
     * Test that updating an existing relation between two objects does not enforce presence of required
     * parameters that were previously set.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testUpdateParamsNotRequired()
    {
        $relation = $this->Relations->find()
            ->where(['params IS NOT' => null])
            ->firstOrFail();

        $objectRelation = $this->ObjectRelations->find()
            ->where(['relation_id' => $relation->id])
            ->firstOrFail();
        $objectRelation->set('params', ['name' => 'Gustavo']);
        $this->ObjectRelations->saveOrFail($objectRelation);

        $currentPriority = $objectRelation->get('priority');

        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $leftType = $ObjectTypes->find('all')
            ->where([
                'id' => $this->ObjectRelations->LeftObjects->get($objectRelation->get('left_id'))->get('object_type_id'),
            ])
            ->firstOrFail()
            ->get('name');
        $rightType = $ObjectTypes->find('all')
            ->where([
                'id' => $this->ObjectRelations->RightObjects->get($objectRelation->get('right_id'))->get('object_type_id'),
            ])
            ->firstOrFail()
            ->get('name');

        $data = [
            [
                'id' => $objectRelation->get('right_id'),
                'type' => $rightType,
                'meta' => [
                    'relation' => [
                        'priority' => $currentPriority + 1,
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $endpoint = sprintf('/%s/%d/relationships/%s', $leftType, $objectRelation->get('left_id'), $relation->get('name'));
        $this->_sendRequest($endpoint, 'POST', json_encode(compact('data')));

        $this->assertResponseSuccess();

        $objectRelation = $this->ObjectRelations->find()
            ->where($objectRelation->extract(['left_id', 'relation_id', 'right_id']))
            ->firstOrFail();

        static::assertSame($currentPriority + 1, $objectRelation->get('priority'));
        static::assertSame(['name' => 'Gustavo'], $objectRelation->get('params'));
    }

    /**
     * Test that creating a new relation does not enforce presence of parameters if they are allowed to be empty.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testOkNoParams()
    {
        // Update Relation settings and force-reload tables.
        $relation = $this->Relations->find()
            ->where(['name' => 'another_test'])
            ->firstOrFail();
        $relation->set('params', [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                ],
                'age' => [
                    'type' => 'integer',
                    'minimum' => 0,
                ],
            ],
        ]);
        $this->Relations->saveOrFail($relation);
        TableRegistry::getTableLocator()->clear();
        $this->Locations = TableRegistry::getTableLocator()->get('Locations');

        // Create new location.
        $firstUser = $this->Users->find()->first();
        $secondLocation = $this->Locations->newEntity(['title' => 'Another location']);
        $secondLocation->created_by = 1;
        $secondLocation->modified_by = 1;
        $secondLocation = $this->Locations->saveOrFail($secondLocation);

        $data = [
            [
                'id' => $secondLocation->id,
                'type' => 'locations',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $endpoint = sprintf('/users/%d/relationships/%s', $firstUser->id, $relation->get('name'));
        $this->_sendRequest($endpoint, 'POST', json_encode(compact('data')));

        $this->assertResponseCode(200);

        $existing = $this->ObjectRelations->exists([
            'left_id' => $firstUser->id,
            'right_id' => $secondLocation->id,
            'relation_id' => $relation->id,
        ]);
        static::assertTrue($existing);
    }

    /**
     * Test patch relationships.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testPatch()
    {
        $data = [
            [
                'id' => '5',
                'type' => 'users',
                'meta' => [
                    'relation' => [
                        'params' => [
                            'name' => 'Gustavo',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $endpoint = sprintf('/locations/8/relationships/inverse_another_test');
        $this->patch($endpoint, json_encode(compact('data')));

        $this->assertResponseCode(200);

        $related = $this->ObjectRelations->find()
            ->where([
                'relation_id' => 2,
                'right_id' => 8,
            ])
            ->toArray();
        static::assertEquals(1, count($related));
        static::assertEquals(5, $related[0]->get('left_id'));
    }

    /**
     * Test patch relationships passing uname in place of id.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testPatchUname(): void
    {
        $data = [
            [
                'id' => 'second-user',
                'type' => 'users',
                'meta' => [
                    'relation' => [
                        'params' => [
                            'name' => 'Gustavo',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $endpoint = sprintf('/locations/8/relationships/inverse_another_test');
        $this->patch($endpoint, json_encode(compact('data')));

        $this->assertResponseCode(200);

        $related = $this->ObjectRelations->find()
            ->where([
                'relation_id' => 2,
                'right_id' => 8,
            ])
            ->toArray();
        static::assertEquals(1, count($related));
        static::assertEquals(5, $related[0]->get('left_id'));
    }
}
