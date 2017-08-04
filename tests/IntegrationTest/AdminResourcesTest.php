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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config'
    ];

    /**
     * Data provider for `testResource`
     *
     * @return array
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
            'jobs' => [
                'async_jobs',
                [
                    'service' => 'dummy',
                ],
                [
                    'priority' => 2,
                 ],
            ],
            'endpoints' => [
                'endpoints',
                [
                    'name' => 'dummy',
                ],
                [
                    'description' => 'a new endpoint description',
                 ],
            ],
            'config' => [
                'config',
                [
                    'name' => 'TestConf',
                    'context' => 'test',
                    'content' => 'Test Configuration',
                ],
                [
                    'content' => 'Another Configuration',
                 ],
            ],
        ];
    }

    /**
     * Test CRUD on admin resources
     *
     * @param $type string Resource type name
     * @param $attributes array Resource attributes to insert
     * @param $modified array Resource attributes to modify
     * @return void
     *
     * @dataProvider resourceProvider
     * @coversNothing
     */
    public function testResource($type, $attributes, $modified)
    {
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

        $locationHeader = $this->_response->getHeaderLine('Location');
        $this->assertNotEmpty($locationHeader);
        $resourceId = substr($locationHeader, strrpos($locationHeader, '/') + 1);

        // VIEW
        $this->configRequestHeaders();
        $this->get("$endpoint/$resourceId");
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // EDIT
        $data = [
            'id' => "$resourceId",
            'type' => $type,
            'attributes' => $modified,
        ];
        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch("$endpoint/$resourceId", json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // DELETE
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete("$endpoint/$resourceId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }
}
