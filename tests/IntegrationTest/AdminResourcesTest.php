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
use Cake\Utility\Inflector;

/**
 * Test CRUD operations on /admin resources
 *
 */
class AdminResourcesTest extends IntegrationTestCase
{

    /**
     * Data provider for `testResource`
     */
    public function resourceProvider()
    {
        return [
            'apps' => [
                'applications',
                [
                    'name' => 'new app name',
                    'description' => 'App desc',
                ],
                [
                    'description' => 'New app desc',
                    'enabled' => 0,
                 ],
            ],
        ];
    }

    /**
     * Test CRUD on admin resources
     *
     * @param $attributes array Resource attributes to insert
     * @param $modified array Resource attributes to modify
     * @dataProvider resourceProvider
     * @coversNothing
     */
    public function testResource($type, $attributes, $modified)
    {
        $lastId = $this->lastResourceId(Inflector::camelize($type));

        // CREATE
        $data = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('POST', $authHeader);
        $endpoint = "/admin/$type";
        $this->post($endpoint, json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        // VIEW
        $this->configRequestHeaders();
        $lastId++;
        $this->get("$endpoint/$lastId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // EDIT
        $data = [
            'id' => "$lastId",
            'type' => $type,
            'attributes' => $modified,
        ];
        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch("$endpoint/$lastId", json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // DELETE
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete("$endpoint/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }
}
