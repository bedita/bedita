<?php
declare(strict_types=1);

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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Endpoint;
use BEdita\Core\Model\Entity\ObjectType;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Endpoint} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Endpoint
 */
class EndpointTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\EndpointsTable
     */
    public $Endpoints;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Endpoints',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Endpoints = TableRegistry::getTableLocator()->get('Endpoints');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Endpoints);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $endpoint = $this->Endpoints->get(1);

        $created = $endpoint->created;
        $modified = $endpoint->modified;

        $data = [
            'id' => 42,
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
        ];
        $endpoint = $this->Endpoints->patchEntity($endpoint, $data);
        if (!($endpoint instanceof Endpoint)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $endpoint->id);
        $this->assertEquals($created, $endpoint->created);
        $this->assertEquals($modified, $endpoint->modified);
    }

    /**
     * Data provder for `testSetObjectTypeName()`
     *
     * @return array
     */
    public function setObjectTypeNameProvider(): array
    {
        return [
            'null' => [
                null,
                null,
            ],
            'name' => [
                2,
                'documents',
            ],
            'singular name' => [
                2,
                'document',
            ],
            'not valid name' => [
                new RecordNotFoundException('Record not found in table "object_types"'),
                'dontfindme',
            ],
        ];
    }

    /**
     * Test magic setter for object_type_name.
     *
     * @param mixed $expected The expected data
     * @param string $name The object type name
     * @return void
     * @covers ::_setObjectTypeName()
     * @dataProvider setObjectTypeNameProvider()
     */
    public function testSetObjectTypeName($expected, ?string $name): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(RecordNotFoundException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $entity = new Endpoint();
        $entity->set('object_type_name', $name);
        $objectType = $entity->object_type;
        if ($expected === null) {
            static::assertNull($objectType);
            static::assertNull($entity->object_type_id);

            return;
        }

        static::assertInstanceOf(ObjectType::class, $objectType);
        static::assertEquals($expected, $objectType->id);
    }
}
