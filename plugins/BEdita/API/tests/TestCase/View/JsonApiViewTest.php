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
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\API\View\JsonApiView
 */
class JsonApiViewTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get('Roles');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Roles);

        parent::tearDown();
    }

    /**
     * Data provider for `testRenderWithoutView` test case.
     *
     * @return array
     */
    public function renderWithoutViewProvider()
    {
        return [
            'data' => [
                json_encode([
                    'data' => [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'first role',
                            'description' => 'this is the very first role',
                        ],
                        'meta' => [
                            'unchangeable' => true,
                            'created' => '2016-04-15T09:57:38+00:00',
                            'modified' => '2016-04-15T09:57:38+00:00',
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                ],
                            ],
                        ],
                    ],
                ]),
                function (Table $Table) {
                    return [
                        'object' => $Table->get(1),
                        '_serialize' => true,
                    ];
                },
            ],
            'dataLinks' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/objects/1',
                    ],
                    'data' => [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'first role',
                            'description' => 'this is the very first role',
                        ],
                        'meta' => [
                            'unchangeable' => true,
                            'created' => '2016-04-15T09:57:38+00:00',
                            'modified' => '2016-04-15T09:57:38+00:00',
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                ],
                            ],
                        ],
                    ],
                ]),
                function (Table $Table) {
                    return [
                        'object' => $Table->get(1),
                        '_links' => [
                            'self' => 'http://example.com/objects/1',
                        ],
                        '_serialize' => true,
                    ];
                },
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
    public function testRenderWithoutView($expected, $data)
    {
        if (is_callable($data)) {
            $data = $data($this->Roles);
        }

        $Controller = new Controller(new Request(), new Response());
        $Controller->set($data);
        $Controller->viewBuilder()->setClassName('BEdita/API.JsonApi');

        $result = $Controller->createView()->render(false);

        static::assertJsonStringEqualsJsonString($expected, $result);
    }
}
