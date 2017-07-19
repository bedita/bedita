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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\HomeController
 */
class HomeControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::objectTypesEndpoints()
     * @covers ::checkAuthorization()
     * @covers ::unloggedAuthorized()
     */
    public function testIndex()
    {
        $project = Configure::read('Project');
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/home',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'resources' => [
                    '/auth' => [
                        'href' => 'http://api.example.com/auth',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Auth',
                            ]
                        ],
                    ],
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
                            ]
                        ],
                    ],
                    '/status' => [
                        'href' => 'http://api.example.com/status',
                        'hints' => [
                            'allow' => [
                                'GET'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Status',
                            ]
                        ],
                    ],
                    '/signup' => [
                        'href' => 'http://api.example.com/signup',
                        'hints' => [
                            'allow' => [
                                'POST'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Signup',
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
                            ]
                        ],
                    ],
                ],
                'project' => $project,
            ],
        ];

        LoggedUser::setUser(['id' => 1]);

        $this->configRequestHeaders();
        $this->get('/home');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);

        LoggedUser::resetUser();
        $this->configRequestHeaders();
        $this->get('/home');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $resetExpect = Hash::remove($expected, 'meta.resources.{*}.hints.allow');
        $resetExpect = Hash::insert($resetExpect, 'meta.resources.{*}.hints.allow', ['GET']);
        $resetExpect = Hash::insert($resetExpect, 'meta.resources./auth.hints.allow', ['POST']);
        $resetExpect = Hash::insert($resetExpect, 'meta.resources./signup.hints.allow', ['POST']);

        $this->assertEquals($resetExpect, $result);
    }
}
