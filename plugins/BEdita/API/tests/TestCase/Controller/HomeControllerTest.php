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
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
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
     * @covers ::endpointFeatures()
     * @covers ::objectTypesEndpoints()
     * @covers ::checkAuthorization()
     * @covers ::unloggedAuthorized()
     */
    public function testIndex()
    {
        $project = Configure::read('Project');
        $version = Configure::read('BEdita.version');
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
                            ],
                            'object_type' => false,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
                        ],
                    ],
                    '/objects' => [
                        'href' => 'http://api.example.com/objects',
                        'hints' => [
                            'allow' => [
                                'GET', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Objects',
                            ],
                            'object_type' => true,
                            'multiple_types' => true,
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
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => false,
                            'multiple_types' => false,
                        ],
                    ],
                    '/model' => [
                        'href' => 'http://api.example.com/model',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Model',
                            ],
                            'object_type' => false,
                            'multiple_types' => true,
                        ],
                    ],
                    '/admin' => [
                        'href' => 'http://api.example.com/admin',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ],
                            'display' => [
                                'label' => 'Admin',
                            ],
                            'object_type' => false,
                            'multiple_types' => true,
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
                            ],
                            'object_type' => false,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => false,
                            'multiple_types' => false,
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
                            ],
                            'object_type' => false,
                            'multiple_types' => true,
                        ],
                    ],
                    '/media' => [
                        'href' => 'http://api.example.com/media',
                        'hints' => [
                            'allow' => [
                                'GET', 'DELETE',
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json',
                            ],
                            'display' => [
                                'label' => 'Media',
                            ],
                            'object_type' => true,
                            'multiple_types' => true,
                        ],
                    ],
                    '/files' => [
                        'href' => 'http://api.example.com/files',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE',
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json',
                            ],
                            'display' => [
                                'label' => 'Files',
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
                        ],
                    ],
                    '/folders' => [
                        'href' => 'http://api.example.com/folders',
                        'hints' => [
                            'allow' => [
                                'GET', 'POST', 'PATCH', 'DELETE',
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json',
                            ],
                            'display' => [
                                'label' => 'Folders',
                            ],
                            'object_type' => true,
                            'multiple_types' => false,
                        ],
                    ],
                ],
                'project' => $project,
                'version' => $version,
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
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

    /**
     * Test `/home` endpoint with blocked `/documents` endpoint
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::endpointFeatures()
     */
    public function testBlockedEndpoint(): void
    {
        $Endpoints = TableRegistry::getTableLocator()->get('Endpoints');
        $endpoint = $Endpoints->newEntity(['name' => 'documents']);
        $endpoint = $Endpoints->saveOrFail($endpoint);

        // setup new permission to block `/documents` endpoint
        $EndpointPermissions = TableRegistry::getTableLocator()->get('EndpointPermissions');
        $EndpointPermissions->deleteAll([]);
        $permission = $EndpointPermissions->newEntity();
        $permission->permission = 0b0000;
        $permission->application_id = 1;
        $permission->role_id = 2;
        $permission->endpoint_id = $endpoint->id;
        $EndpointPermissions->saveOrFail($permission);

        CurrentApplication::setFromApiKey(API_KEY);
        $this->configRequestHeaders('GET', $this->getUserAuthHeader('second user', 'password2'));
        $this->get('/home');
        $result = json_decode((string)$this->_response->getBody(), true);
        $meta = Hash::get($result, 'meta.resources./documents');
        static::assertNull($meta);
    }
}
