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
    public function setUp()
    {
        parent::setUp();

        $this->Locations = TableRegistry::get('Locations');
        $this->Users = TableRegistry::get('Users');
        $this->Relations = TableRegistry::get('Relations');
        $this->ObjectRelations = TableRegistry::get('ObjectRelations');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
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
}
