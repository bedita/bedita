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

namespace BEdita\API\Test\TestCase\View;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\API\View\JsonApiView
 */
class JsonApiViewTest extends TestCase
{

    /**
     * Data provider for `testRenderWithoutView` test case.
     *
     * @return array
     */
    public function renderWithoutViewProvider()
    {
        return [
            'dataType' => [
                json_encode([
                    'data' => [
                        'id' => '1',
                        'type' => 'myType',
                        'attributes' => [
                            'myAttribute' => 'myValue',
                        ],
                    ],
                ]),
                [
                    '_type' => 'myType',
                    'object' => [
                        'id' => 1,
                        'myAttribute' => 'myValue',
                    ],
                    '_serialize' => true,
                ],
            ],
            'dataLinks' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/objects/1',
                    ],
                    'data' => [
                        'id' => '1',
                        'type' => 'myType',
                        'attributes' => [
                            'myAttribute' => 'myValue',
                        ],
                    ],
                ]),
                [
                    'object' => [
                        'id' => 1,
                        'type' => 'myType',
                        'myAttribute' => 'myValue',
                    ],
                    '_links' => [
                        'self' => 'http://example.com/objects/1',
                    ],
                    '_serialize' => true,
                ],
            ],
            'linksMeta' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/objects',
                    ],
                    'meta' => [
                        'metaKey' => 'metaValue',
                    ],
                    'data' => [],
                ]),
                [
                    '_links' => [
                        'self' => 'http://example.com/objects',
                    ],
                    '_meta' => [
                        'metaKey' => 'metaValue',
                    ],
                    '_serialize' => true,
                ],
            ],
            'dataLinksError' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/objects/0',
                    ],
                    'error' => [
                        'status' => '404',
                        'title' => 'Not found',
                        'description' => 'The requested object could not be found',
                    ],
                ]),
                [
                    '_links' => [
                        'self' => 'http://example.com/objects/0',
                    ],
                    'object' => 'I do not even exist!',
                    '_serialize' => true,
                    '_error' => [
                        'status' => 404,
                        'title' => 'Not found',
                        'description' => 'The requested object could not be found',
                    ],
                ],
            ],
            'noData' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/home',
                    ],
                    'meta' => [
                        'metaKey' => 'metaValue',
                    ],
                ]),
                [
                    '_links' => [
                        'self' => 'http://example.com/home',
                    ],
                    '_meta' => [
                        'metaKey' => 'metaValue',
                    ],
                    '_serialize' => [],
                ],
            ],
        ];
    }

    /**
     * Test render JSON API view.
     *
     * @param string $expected Expected output.
     * @param array $data Variables to be set in controller.
     * @return void
     *
     * @dataProvider renderWithoutViewProvider
     */
    public function testRenderWithoutView($expected, array $data)
    {
        $Controller = new Controller(new Request(), new Response());
        $Controller->set($data);
        $Controller->viewBuilder()->className('BEdita/API.JsonApi');

        $result = $Controller->createView()->render(false);

        $this->assertJsonStringEqualsJsonString($expected, $result);
    }
}
