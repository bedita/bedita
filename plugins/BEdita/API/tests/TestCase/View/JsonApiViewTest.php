<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\View;

use BEdita\API\Test\TestConstants;
use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::getTableLocator()->get('Roles');
        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
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
                    'meta' => [
                        'schema' => [
                            'roles' => [
                                '$id' => '/model/schema/roles',
                                'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
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
                    'meta' => [
                        'schema' => [
                            'roles' => [
                                '$id' => '/model/schema/roles',
                                'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
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
            'commonFields' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/objects',
                    ],
                ]),
                [
                    '_links' => [
                        'self' => 'http://example.com/objects',
                    ],
                    '_fields' => 'title,descritpion',
                    '_serialize' => [],
                ],
            ],
            'sparseFields' => [
                json_encode([
                    'links' => [
                        'self' => 'http://example.com/roles',
                    ],
                ]),
                [
                    '_links' => [
                        'self' => 'http://example.com/roles',
                    ],
                    '_fields' => 'name,descritpion',
                    '_serialize' => [],
                ],
            ],
            'included' => [
                json_encode([
                    'data' => [
                        [
                            'id' => '1',
                            'type' => 'roles',
                            'links' => [
                                'self' => '/roles/1',
                            ],
                            'relationships' => [
                                'users' => [
                                    'data' => [
                                       [
                                            'id' => '1',
                                            'type' => 'users'
                                       ],
                                    ],
                                    'links' => [
                                        'related' => '/roles/1/users',
                                        'self' => '/roles/1/relationships/users'
                                    ]
                                 ],
                            ],
                        ],
                        [
                            'id' => '2',
                            'type' => 'roles',
                            'links' => [
                                'self' => '/roles/2',
                            ],
                            'relationships' => [
                                'users' => [
                                    'data' => [
                                       [
                                            'id' => '5',
                                            'type' => 'users'
                                       ],
                                    ],
                                    'links' => [
                                        'related' => '/roles/2/users',
                                        'self' => '/roles/2/relationships/users'
                                    ]
                                ],
                            ],
                        ],
                        [
                            'id' => '3',
                            'type' => 'roles',
                            'links' => [
                                'self' => '/roles/3',
                            ],
                            'relationships' => [
                                'users' => [
                                    'data' => [
                                        [
                                            'id' => '1',
                                            'type' => 'users',
                                        ],
                                    ],
                                    'links' => [
                                        'related' => '/roles/3/users',
                                        'self' => '/roles/3/relationships/users',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'meta' => [
                        'schema' => [
                            'users' => [
                                '$id' => '/model/schema/users',
                                'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'id' => '1',
                            'type' => 'users',
                            'meta' => [
                                'external_auth' => [
                                    [
                                        'provider' => 'example',
                                        'username' => 'first_user',
                                    ],
                                ],
                            ],
                            'links' => [
                                'self' => '/users/1',
                            ],
                            'relationships' => [
                                'another_test' => [
                                    'links' => [
                                        'related' => '/users/1/another_test',
                                        'self' => '/users/1/relationships/another_test',
                                    ],
                                ],
                                'roles' => [
                                    'links' => [
                                        'related' => '/users/1/roles',
                                        'self' => '/users/1/relationships/roles',
                                    ],
                                ],
                                'parents' => [
                                    'links' => [
                                        'related' => '/users/1/parents',
                                        'self' => '/users/1/relationships/parents',
                                    ],
                                ],
                                'translations' => [
                                    'links' => [
                                        'related' => '/users/1/translations',
                                        'self' => '/users/1/relationships/translations',
                                    ],
                                ],
                                'placeholder' => [
                                    'links' => [
                                        'related' => '/users/1/placeholder',
                                        'self' => '/users/1/relationships/placeholder',
                                    ],
                                ],
                                'placeholded' => [
                                    'links' => [
                                        'related' => '/users/1/placeholded',
                                        'self' => '/users/1/relationships/placeholded',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'id' => '5',
                            'type' => 'users',
                            'meta' => [
                                'external_auth' => [
                                    [
                                        'provider' => 'uuid',
                                        'username' => '17fec0fa-068a-4d7c-8283-da91d47cef7d',
                                    ],
                                ],
                            ],
                            'links' => [
                                'self' => '/users/5',
                            ],
                            'relationships' => [
                                'another_test' => [
                                    'links' => [
                                        'related' => '/users/5/another_test',
                                        'self' => '/users/5/relationships/another_test',
                                    ],
                                ],
                                'roles' => [
                                    'links' => [
                                        'related' => '/users/5/roles',
                                        'self' => '/users/5/relationships/roles',
                                    ],
                                ],
                                'parents' => [
                                    'links' => [
                                        'related' => '/users/5/parents',
                                        'self' => '/users/5/relationships/parents',
                                    ],
                                ],
                                'translations' => [
                                    'links' => [
                                        'related' => '/users/5/translations',
                                        'self' => '/users/5/relationships/translations',
                                    ],
                                ],
                                'placeholder' => [
                                    'links' => [
                                        'related' => '/users/5/placeholder',
                                        'self' => '/users/5/relationships/placeholder',
                                    ],
                                ],
                                'placeholded' => [
                                    'links' => [
                                        'related' => '/users/5/placeholded',
                                        'self' => '/users/5/relationships/placeholded',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                function (Table $Table) {
                    return [
                        'objects' => [
                            $Table->get(1, ['contain' => 'Users']),
                            $Table->get(2, ['contain' => 'Users']),
                            $Table->get(1, ['contain' => 'Users'])->set('id', 3),
                        ],
                        '_serialize' => true,
                        '_fields' => [
                            'roles' => '',
                            'users' => '',
                        ],
                    ];
                },
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

        $Controller = new Controller(new ServerRequest(), new Response());
        $Controller->set($data);
        $Controller->viewBuilder()->setClassName('BEdita/API.JsonApi');

        $result = $Controller->createView()->render(false);

        static::assertJsonStringEqualsJsonString($expected, $result);
    }

    /**
     * Test 'json' response in constructor
     *
     * @return void
     */
    public function testJsonRequest()
    {
        $request = new ServerRequest([
            'environment' => [
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $Controller = new Controller($request, new Response());
        $Controller->viewBuilder()->setClassName('BEdita/API.JsonApi');
        $view = $Controller->createView();
        static::assertEquals('application/json', $view->getResponse()->getType());
    }
}
