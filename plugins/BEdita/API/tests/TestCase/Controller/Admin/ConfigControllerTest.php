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

namespace BEdita\API\Test\TestCase\Controller\Admin;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\ConfigController
 */
class ConfigControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
    ];

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/config/Name1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => 'Name1',
                'type' => 'config',
                'attributes' => [
                    'context' => 'group1',
                    'content' => 'data',
                    'application_id' => null,
                ],
                'meta' => [
                    'created' => '2016-06-16T12:34:56+00:00',
                    'modified' => '2016-06-16T12:38:02+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/config/Name1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test add method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'id' => 'NewConfig',
            'type' => 'config',
            'attributes' => [
                'name' => 'NewConfig',
                'context' => 'new',
                'content' => 'new value',
                'application_id' => null,
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/config', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $this->assertHeader('Location', 'http://api.example.com/admin/config/' . $data['id']);
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => 'Name1',
            'type' => 'config',
            'attributes' => [
                'content' => 'data2',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/config/Name1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $entity = TableRegistry::get('Config')->get('Name1');
        static::assertEquals('data2', $entity->get('content'));
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/admin/config/Name2');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        static::assertFalse(TableRegistry::get('Config')->exists(['name' => 'Name2']));
    }
}
