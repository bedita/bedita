<?php
/**
 * BEdita - a semantic content management framework
 * Copyright (C) 2008-2016  Chia Lab s.r.l., Channelweb s.r.l.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
