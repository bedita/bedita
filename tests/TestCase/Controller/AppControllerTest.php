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
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotAcceptableException;

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
     * Data provider for `testGetApplication` test case.
     *
     * @return array
     */
    public function getApplicationProvider()
    {
        return [
            'standard' => [
                1,
                [
                    'HTTP_X_API_KEY' => API_KEY,
                ],
            ],
            'invalid API key' => [
                new ForbiddenException('Invalid API key'),
                [
                    'HTTP_X_API_KEY' => 'this API key is invalid!',
                ],
            ],
            'missing API key' => [
                new ForbiddenException('Missing API key'),
                [],
                [],
                true,
            ],
            'anonymous application' => [
                null,
                [],
            ],
            'query string api key' => [
                1,
                [],
                [
                    'api_key' => API_KEY,
                ],
            ],
            'query string failure' => [
                new ForbiddenException('Invalid API key'),
                [],
                [
                    'api_key' => 'this API key is invalid!',
                ]
            ],
        ];
    }

    /**
     * Test getting application from request headers.
     *
     * @param int|\Exception $expected Expected application ID.
     * @param array $environment Request headers.
     * @param array $query Request query strings.
     * @param bool $blockAnonymous Block anonymous apps flag.
     * @return void
     *
     * @dataProvider getApplicationProvider()
     * @covers ::getApplication()
     */
    public function testGetApplication($expected, array $environment, array $query = [], $blockAnonymous = false)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        Configure::write('Security.blockAnonymousApps', $blockAnonymous);
        CurrentApplication::getInstance()->set(null);
        $environment += ['HTTP_ACCEPT' => 'application/json'];
        $request = new ServerRequest(compact('environment', 'query'));

        $controller = new AppController($request);
        $controller->dispatchEvent('Controller.initialize');

        static::assertEquals($expected, CurrentApplication::getApplicationId());
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
