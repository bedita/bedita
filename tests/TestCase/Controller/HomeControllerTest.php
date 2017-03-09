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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\Core\State\CurrentApplication;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\HomeController
 */
class HomeControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        CurrentApplication::setFromApiKey(API_KEY);
    }

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/home',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'resources' => [
                    '/documents' => [
                        'href' => 'http://api.example.com/documents',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Documents',
                                'color' => '#852a36'
                            ]
                        ],
                    ],
                    '/profiles' => [
                        'href' => 'http://api.example.com/profiles',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Profiles',
                                'color' => '#622635'
                            ]
                        ],
                    ],
                    '/objects' => [
                        'href' => 'http://api.example.com/objects',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Objects',
                                'color' => '#6103c1'
                            ]
                        ],
                    ],
                    '/users' => [
                        'href' => 'http://api.example.com/users',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Users',
                                'color' => '#032813'
                            ]
                        ],
                    ],
                    '/news' => [
                        'href' => 'http://api.example.com/news',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'News',
                                'color' => '#63e1d8'
                            ]
                        ],
                    ],
                    '/locations' => [
                        'href' => 'http://api.example.com/locations',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Locations',
                                'color' => '#cc324f'
                            ]
                        ],
                    ],
                    '/events' => [
                        'href' => 'http://api.example.com/events',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Events',
                                'color' => '#44d1ab'
                            ]
                        ],
                    ],
                    '/roles' => [
                        'href' => 'http://api.example.com/roles',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Roles',
                                'color' => '#7b2bac'
                            ]
                        ],
                    ],
                    '/object_types' => [
                        'href' => 'http://api.example.com/object_types',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'ObjectTypes',
                                'color' => '#6b2d02'
                            ]
                        ],
                    ],
                    '/status' => [
                        'href' => 'http://api.example.com/status',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Status',
                                'color' => '#88206a'
                            ]
                        ],
                    ],
                    '/trash' => [
                        'href' => 'http://api.example.com/trash',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Trash',
                                'color' => '#f45336'
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/home');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
