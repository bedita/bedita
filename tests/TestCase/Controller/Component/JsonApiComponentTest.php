<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller\Component;

use BEdita\API\Controller\Component\JsonApiComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\Component\JsonApiComponent
 */
class JsonApiComponentTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public $autoFixtures = false;

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Router::fullBaseUrl('http://example.org');
    }

    /**
     * Data provider for `testInitialize` test case.
     *
     * @return array
     */
    public function initializeProvider()
    {
        return [
            'default' => [
                'application/vnd.api+json',
                [],
            ],
            'json' => [
                'application/json',
                [
                    'contentType' => 'application/json',
                ],
            ],
        ];
    }

    /**
     * Test component initialization.
     *
     * @param string $expectedMimeType Expected response MIME Type.
     * @param array $config Component configuration.
     * @return void
     *
     * @dataProvider initializeProvider
     * @covers ::initialize()
     */
    public function testInitialize($expectedMimeType, array $config)
    {
        $component = new JsonApiComponent(new ComponentRegistry(new Controller()), $config);

        $this->assertEquals($expectedMimeType, $component->response->getMimeType('jsonapi'));
        $this->assertArrayHasKey('jsonapi', $component->RequestHandler->config('inputTypeMap'));
        $this->assertArrayHasKey('jsonapi', $component->RequestHandler->config('viewClassMap'));
    }

    /**
     * Test component `getLinks()` method.
     *
     * @return void
     *
     * @covers ::getLinks()
     */
    public function testLinks()
    {
        $expected = [
            'self' => 'http://example.org/users',
            'home' => 'http://example.org/home',
        ];

        $request = new Request([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Users',
                'action' => 'index',
                '_method' => 'GET',
            ],
            'base' => '/',
            'url' => 'users',
        ]);
        $controller = new Controller($request);
        $component = new JsonApiComponent(new ComponentRegistry($controller), []);

        $this->assertEquals($expected, $component->getLinks());
    }

    /**
     * Data provider for `testPagination` test case.
     *
     * @return array
     */
    public function paginationProvider()
    {
        return [
            'default' => [
                [
                    'self' => 'http://example.org/users',
                    'first' => 'http://example.org/users',
                    'last' => 'http://example.org/users',
                    'prev' => null,
                    'next' => null,
                    'home' => 'http://example.org/home',
                ],
                [
                    'pagination' => [
                        'count' => 2,
                        'page' => 1,
                        'page_count' => 1,
                        'page_items' => 2,
                        'page_size' => 20,
                    ],
                ],
                [],
            ],
            'limit' => [
                [
                    'self' => 'http://example.org/users?limit=1',
                    'first' => 'http://example.org/users?limit=1',
                    'last' => 'http://example.org/users?limit=1&page=2',
                    'prev' => null,
                    'next' => 'http://example.org/users?limit=1&page=2',
                    'home' => 'http://example.org/home',
                ],
                [
                    'pagination' => [
                        'count' => 2,
                        'page' => 1,
                        'page_count' => 2,
                        'page_items' => 1,
                        'page_size' => 1,
                    ],
                ],
                [
                    'limit' => 1,
                ],
            ],
            'page' => [
                [
                    'self' => 'http://example.org/users?page=2&limit=1',
                    'first' => 'http://example.org/users?limit=1',
                    'last' => 'http://example.org/users?page=2&limit=1',
                    'prev' => 'http://example.org/users?limit=1',
                    'next' => null,
                    'home' => 'http://example.org/home',
                ],
                [
                    'pagination' => [
                        'count' => 2,
                        'page' => 2,
                        'page_count' => 2,
                        'page_items' => 1,
                        'page_size' => 1,
                    ],
                ],
                [
                    'page' => 2,
                    'limit' => 1,
                ],
            ],
        ];
    }

    /**
     * Test component `getLinks()` and `getMeta()` methods with pagination.
     *
     * @param array $expectedLinks Expected links array.
     * @param array $expectedMeta Expected meta array.
     * @param array $query Request query params.
     * @return void
     *
     * @dataProvider paginationProvider
     * @covers ::getLinks()
     * @covers ::getMeta()
     */
    public function testPagination(array $expectedLinks, array $expectedMeta, array $query)
    {
        $this->loadFixtures('Users');

        $request = new Request([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Users',
                'action' => 'index',
                '_method' => 'GET',
            ],
            'base' => '/',
            'url' => 'users',
            'query' => $query,
        ]);
        $controller = new Controller($request);
        $controller->paginate(TableRegistry::get('Users'));
        $component = new JsonApiComponent(new ComponentRegistry($controller), []);

        $this->assertEquals($expectedLinks, $component->getLinks());
        $this->assertEquals($expectedMeta, $component->getMeta());
    }

    /**
     * Test `error()` method.
     *
     * @return void
     *
     * @covers ::error()
     */
    public function testError()
    {
        $expected = [
            'status' => '500',
            'title' => 'Example error',
            'description' => 'Example description',
            'meta' => [
                'key' => 'Example metadata',
            ],
        ];

        $controller = new Controller();
        $component = new JsonApiComponent(new ComponentRegistry($controller), []);

        $component->error(500, 'Example error', 'Example description', ['key' => 'Example metadata']);

        $this->assertEquals($expected, $controller->viewVars['_error']);
    }

    /**
     * Data provider for `testParseInput` test case.
     *
     * @return array
     */
    public function parseInputProvider()
    {
        return [
            'valid' => [
                [
                    'type' => 'customType',
                    'key' => 'value',
                ],
                '{"data":{"type":"customType","attributes":{"key":"value"}}}'
            ],
            'invalidJson' => [
                [],
                '{"some", "invalid":"json"',
            ],
            'invalidJsonApi' => [
                false,
                '{"data":{"type":null,"attributes":{"key":"value"}}}',
            ],
        ];
    }

    /**
     * Test `parseInput()` method.
     *
     * @param array $expected Expected parsed array.
     * @param string $input Input to be parsed.
     * @return void
     *
     * @dataProvider parseInputProvider
     * @covers ::parseInput()
     */
    public function testParseInput($expected, $input)
    {
        if ($expected === false) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $component = new JsonApiComponent(new ComponentRegistry(new Controller()));

        $result = $component->parseInput($input);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testAllowedResourceTypes` test case.
     *
     * @return array
     */
    public function allowedResourceTypesProvider()
    {
        return [
            'single' => [
                true,
                'myCustomType',
                [
                    'type' => 'myCustomType',
                    'key' => 'value',
                ],
            ],
            'multiple' => [
                true,
                ['myCustomType1', 'myCustomType2'],
                [
                    [
                        'type' => 'myCustomType1',
                        'key' => 'value',
                    ],
                    [
                        'type' => 'myCustomType2',
                        'key' => 'value',
                    ],
                ],
            ],
            'emptyData' => [
                true,
                ['myCustomType1', 'myCustomType2'],
                [],
            ],
            'emptyTypes' => [
                true,
                null,
                [
                    'type' => 'myCustomType',
                    'key' => 'value',
                ],
            ],
            'unsupportedType' => [
                false,
                ['myCustomType'],
                [
                    'type' => 'unsupportedType',
                    'key' => 'value',
                ],
            ],
        ];
    }

    /**
     * Test `allowedResourceTypes()` method.
     *
     * @param bool $expected Expected success.
     * @param mixed $types Allowed types.
     * @param array $data Data to be checked.
     * @return void
     *
     * @dataProvider allowedResourceTypesProvider
     * @covers ::allowedResourceTypes()
     * @covers ::startup()
     */
    public function testAllowedResourceTypes($expected, $types, array $data)
    {
        if (!$expected) {
            $this->setExpectedException('\Cake\Network\Exception\ConflictException');
        }

        $request = new Request([
            'environment' => [
                'HTTP_ACCEPT' => 'application/vnd.api+json',
                'HTTP_CONTENT_TYPE' => 'application/vnd.api+json',
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
        ]);

        $controller = new Controller($request);
        $component = new JsonApiComponent(new ComponentRegistry($controller), ['resourceTypes' => $types]);

        $component->startup(new Event('Controller.startup', $controller));

        $this->assertTrue(true);
    }

    /**
     * Data provider for `testAllowClientGeneratedIds` test case.
     *
     * @return array
     */
    public function allowClientGeneratedIdsProvider()
    {
        return [
            'single' => [
                true,
                [
                    'type' => 'myCustomType',
                    'key' => 'value',
                ],
            ],
            'multiple' => [
                true,
                [
                    [
                        'type' => 'myCustomType1',
                        'key' => 'value',
                    ],
                    [
                        'type' => 'myCustomType2',
                        'key' => 'value',
                    ],
                ],
            ],
            'emptyData' => [
                true,
                [],
            ],
            'unsupportedClientGeneratedId' => [
                false,
                [
                    'id' => 'my-id',
                    'type' => 'myCustomType',
                    'key' => 'value',
                ],
            ],
        ];
    }

    /**
     * Test `allowClientGeneratedIds()` method.
     *
     * @param bool $expected Expected success.
     * @param array $data Data to be checked.
     * @return void
     *
     * @dataProvider allowClientGeneratedIdsProvider
     * @covers ::allowClientGeneratedIds()
     * @covers ::startup()
     */
    public function testAllowClientGeneratedIds($expected, array $data)
    {
        if (!$expected) {
            $this->setExpectedException('\Cake\Network\Exception\ForbiddenException');
        }

        $request = new Request([
            'environment' => [
                'HTTP_ACCEPT' => 'application/vnd.api+json',
                'HTTP_CONTENT_TYPE' => 'application/vnd.api+json',
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
        ]);

        $controller = new Controller($request);
        $component = new JsonApiComponent(new ComponentRegistry($controller));

        $component->startup(new Event('Controller.startup', $controller));

        $this->assertTrue(true);
    }
}
