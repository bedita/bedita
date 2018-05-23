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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * Test new object types creation, along with objects implementations
 */
class NewObjectTypesTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Data provider for `testNewTypes`
     */
    public function newTypesProvider()
    {
        return [
            'cats' => [
                [
                    'name' => 'cats',
                    'singular' => 'cat',
                ],
                [
                    'description' => 'another cat',
                ]
            ],
            'songs' => [
                [
                    'name' => 'songs',
                    'singular' => 'song',
                    'table' => 'BEdita/Core.Media',
                    'parent_name' => 'media',
                ],
                [
                    'description' => 'a new song',
                ]
            ],
        ];
    }

    /**
     * Test new object type creation with object implementation
     *
     * @param $typeData array New object type intpu data
     * @param $attributes array New object attributes
     * @dataProvider newTypesProvider
     * @coversNothing
     */
    public function testNewTypes($typeData, $attributes)
    {
        $lastId = $this->lastObjectId();

        // ADD TYPE
        $data = [
            'type' => 'object_types',
            'attributes' => $typeData,
        ];
        $type = $typeData['name'];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/object_types', json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertTrue(TableRegistry::get('ObjectTypes')->exists(['name' => $type]));

        // ADD OBJECT
        $data = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        TableRegistry::clear();
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $endpoint = '/' . $type;
        $this->post($endpoint, json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        // VIEW
        TableRegistry::clear();
        $this->configRequestHeaders();
        $lastId++;
        $this->get("/$type/$lastId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertArrayHasKey('attributes', $result['data']);
        static::assertArraySubset($attributes, $result['data']['attributes']);

        // VIEW FROM PARENT
        TableRegistry::clear();
        $this->configRequestHeaders();
        $parentEndoint = empty($typeData['parent_name']) ? 'objects' : $typeData['parent_name'];
        $this->get("/$parentEndoint/$lastId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertArrayHasKey('attributes', $result['data']);
        static::assertArraySubset($attributes, $result['data']['attributes']);

        // DELETE
        TableRegistry::clear();
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete("/$type/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        // EMPTY TRASH
        TableRegistry::clear();
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete("/trash/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);

        // REMOVE TYPE
        TableRegistry::clear();
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete("/model/object_types/$type");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }
}
