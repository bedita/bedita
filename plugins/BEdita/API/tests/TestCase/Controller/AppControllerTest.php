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

use BEdita\API\Controller\AppController;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\API\Test\TestConstants;
use Cake\Core\Configure;
use Cake\Http\Exception\NotAcceptableException;
use Cake\Http\ServerRequest;

/**
 * @coversDefaultClass \BEdita\API\Controller\AppController
 */
class AppControllerTest extends IntegrationTestCase
{

    /**
     * Test API meta info header.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testMetaInfo()
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
                'X-Api-Key' => API_KEY,
            ],
        ]);

        $this->_sendRequest('/home', 'HEAD');

        $this->assertHeader('X-BEdita-Version', Configure::read('BEdita.version'));
    }

    /**
     * Data provider for `testCheckAccept` test case.
     *
     * @return array
     */
    public function checkAcceptProvider()
    {
        return [
            'ok' => [
                true,
                'application/vnd.api+json',
            ],
            'error (dramatic music)' => [
                new NotAcceptableException('Bad request content type "gustavo/supporto"'),
                'gustavo/supporto',
            ],
        ];
    }

    /**
     * Test accepted content types in `beforeFilter()` method.
     *
     * @param true|\Exception $expected Expected success.
     * @param string $accept Value of "Accept" header.
     * @return void
     *
     * @dataProvider checkAcceptProvider
     * @covers ::beforeFilter()
     */
    public function testCheckAccept($expected, $accept)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $request = new ServerRequest([
            'environment' => [
                'HTTP_ACCEPT' => $accept,
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $controller = new AppController($request);

        $controller->dispatchEvent('Controller.initialize');

        static::assertTrue($expected);
    }

    /**
     * Test included resources.
     *
     * @return void
     *
     * @covers ::prepareInclude()
     */
    public function testInclude()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1?include=users',
                'home' => 'http://api.example.com/home',
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
                            'self' => 'http://api.example.com/roles/1/relationships/users',
                            'related' => 'http://api.example.com/roles/1/users',
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'users',
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'name' => 'First',
                        'surname' => 'User',
                        'email' => 'first.user@example.com',
                        'person_title' => 'Mr.',
                        'gender' => null,
                        'birthdate' => '1945-04-25',
                        'deathdate' => null,
                        'company' => false,
                        'company_name' => null,
                        'company_kind' => null,
                        'street_address' => null,
                        'city' => null,
                        'zipcode' => null,
                        'country' => null,
                        'state_name' => null,
                        'phone' => null,
                        'website' => null,
                        'national_id_number' => null,
                        'vat_number' => null,
                        'status' => 'on',
                        'uname' => 'first-user',
                        'title' => 'Mr. First User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'another_username' => null, // custom property
                        'another_email' => null, // custom property
                    ],
                    'meta' => [
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'verified' => '2017-05-29T11:36:00+00:00',
                        'external_auth' => [
                            [
                                'provider' => 'example',
                                'username' => 'first_user'
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/roles',
                                'self' => 'http://api.example.com/users/1/relationships/roles',
                            ],
                        ],
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/another_test',
                                'self' => 'http://api.example.com/users/1/relationships/another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/parents',
                                'self' => 'http://api.example.com/users/1/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/translations',
                                'self' => 'http://api.example.com/users/1/relationships/translations',
                            ],
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'roles' => [
                        '$id' => 'http://api.example.com/model/schema/roles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                    ],
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/roles/1?include=users');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testIncludeError` test case.
     *
     * @return array
     */
    public function includeErrorProvider()
    {
        return [
            'not a string' => [
                400,
                'Invalid "include" query parameter (Must be a comma-separated string)',
                ['not', 'a', 'string'],
            ],
            'nested resources' => [
                400,
                'Inclusion of nested resources is not yet supported',
                'users.roles',
            ],
            'not found' => [
                400,
                'Invalid "include" query parameter (Relationship "gustavo" does not exist)',
                'users,gustavo',
            ],
        ];
    }

    /**
     * Test included resources.
     *
     * @param int $expectedStatus Expected status.
     * @param string $expectedErrorTitle Expected error message.
     * @param mixed $include `include` query parameter.
     * @return void
     *
     * @dataProvider includeErrorProvider()
     * @covers ::prepareInclude()
     */
    public function testIncludeError($expectedStatus, $expectedErrorTitle, $include)
    {
        $expected = [
            'status' => (string)$expectedStatus,
            'title' => $expectedErrorTitle,
        ];

        $this->configRequestHeaders();
        $this->get('/roles?' . http_build_query(compact('include')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode($expectedStatus);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }

    /**
     * Test that no resources are included unless asked.
     *
     * @return void
     *
     * @covers ::prepareInclude()
     */
    public function testIncludeEmpty()
    {
        $this->configRequestHeaders();
        $this->get('/roles');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayNotHasKey('included', $result);
    }
}
