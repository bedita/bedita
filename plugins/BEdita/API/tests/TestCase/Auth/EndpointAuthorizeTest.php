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

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\EndpointAuthorize;
use BEdita\Core\Model\Entity\Endpoint;
use BEdita\Core\State\CurrentApplication;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Uri;

/**
 * @coversDefaultClass \BEdita\API\Auth\EndpointAuthorize
 */
class EndpointAuthorizeTest extends TestCase
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
            'custom header' => [
                1,
                [
                    'HTTP_X_CUSTOM_HEADER' => API_KEY,
                ],
                [],
                [
                    'apiKeyHeaderName' => 'X-Custom-Header',
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
                [
                    'blockAnonymousApps' => true,
                ],
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
     * @param array $config Configuration.
     * @return void
     *
     * @dataProvider getApplicationProvider()
     * @covers ::getApplication()
     */
    public function testGetApplication($expected, array $environment, array $query = [], array $config = [])
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        CurrentApplication::getInstance()->set(null);
        $authorize = new EndpointAuthorize(new ComponentRegistry(), $config);
        $request = new ServerRequest(compact('environment', 'query'));

        $authorize->authorize([], $request);

        if ($expected === null) {
            static::assertNull(CurrentApplication::getApplication());
        } else {
            static::assertEquals($expected, CurrentApplication::getApplication()->id);
        }
    }

    /**
     * Data provider for `testGetEndpoint` test case.
     *
     * @return array
     */
    public function getEndpointProvider()
    {
        return [
            '/auth' => [
                1,
                new Uri('/auth'),
            ],
            '/home/sweet/home' => [
                2,
                new Uri('/home/sweet/home'),
            ],
            '/' => [
                new Endpoint(
                    [
                        'name' => '',
                        'enabled' => true
                    ],
                    [
                        'source' => 'Endpoints'
                    ]
                ),
                new Uri('/'),
            ],
            '/this/endpoint/definitely/doesnt/exist' => [
                new Endpoint(
                    [
                        'name' => 'this',
                        'enabled' => true
                    ],
                    [
                        'source' => 'Endpoints'
                    ]
                ),
                new Uri('/this/endpoint/definitely/doesnt/exist'),
            ],
            '/disabled/endpoint' => [
                new NotFoundException('Resource not found.'),
                new Uri('/disabled/endpoint'),
            ]
        ];
    }

    /**
     * Test getting endpoint from request.
     *
     * @param mixed $expected Expected endpoint ID, entity, or exception.
     * @param \Psr\Http\Message\UriInterface $uri Request URI.
     * @return void
     *
     * @dataProvider getEndpointProvider()
     * @covers ::getEndpoint()
     */
    public function testGetEndpoint($expected, UriInterface $uri)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        CurrentApplication::setFromApiKey(API_KEY);
        $authorize = new EndpointAuthorize(new ComponentRegistry(), []);
        $request = new ServerRequest(compact('uri'));

        $authorize->authorize([], $request);

        if (is_int($expected)) {
            $expected = TableRegistry::get('Endpoints')->get($expected);
        }

        static::assertAttributeEquals($expected, 'endpoint', $authorize);
    }

    /**
     * Data provider for `testAuthorize` test case.
     *
     * @return array
     */
    public function authorizeProvider()
    {
        return [
            'GET /home (anonymous)' => [
                new UnauthorizedException('Unauthorized'),
                new Uri('/home'),
                [
                    '_anonymous' => true
                ],
            ],
            'POST /home (role_id = 1)' => [
                false,
                new Uri('/home'),
                [
                    'roles' => [
                        [
                            'id' => 1,
                        ],
                    ],
                ],
                'POST',
            ],
            'GET /home (role_id = 1)' => [
                'mine',
                new Uri('/home'),
                [
                    'roles' => [
                        [
                            'id' => 1,
                        ],
                    ],
                ],
            ],
            'GET /unknown-endpoint (anonymous)' => [
                new UnauthorizedException('Unauthorized'),
                new Uri('/unknown-endpoint'),
                [
                    '_anonymous' => true,
                ],
            ],
            'GET /disabled (anonymous)' => [
                new NotFoundException('Resource not found.'),
                new Uri('/disabled'),
                [
                    '_anonymous' => true
                ],
                'GET',
                true
            ],
            'GET /disabled (role_id = 1)' => [
                new NotFoundException('Resource not found.'),
                new Uri('/disabled'),
                [
                    'roles' => [
                        [
                            'id' => 1,
                        ],
                    ],
                ],
            ],
            'POST /signup whitelist (anonymous)' => [
                true,
                new Uri('/signup'),
                [
                    '_anonymous' => true
                ],
                'POST',
                true
            ],
        ];
    }

    /**
     * Test authorization for user.
     *
     * @param bool|\Exception $expected Expected result.
     * @param \Psr\Http\Message\UriInterface $uri Request URI.
     * @param array $user User data.
     * @param string $requestMethod Request method.
     * @param bool $whiteListed Is the endpoint whitelisted?
     * @return void
     *
     * @dataProvider authorizeProvider()
     * @covers ::authorize()
     * @covers ::isAnonymous()
     * @covers ::getPermissions()
     * @covers ::checkPermissions()
     */
    public function testAuthorize($expected, UriInterface $uri, array $user, $requestMethod = 'GET', $whiteListed = false)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        CurrentApplication::setApplication(TableRegistry::get('Applications')->get(2));

        $environment = [
            'REQUEST_METHOD' => $requestMethod,
        ];
        $request = new ServerRequest(compact('environment', 'uri'));

        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => ['BEdita/API.Endpoint'],
        ]);
        $authorize = $controller->Auth->getAuthorize('BEdita/API.Endpoint');
        $authorize->setConfig('defaultAuthorized', $whiteListed);
        $authorize->setConfig('blockAnonymousUsers', false);

        if (!($authorize instanceof EndpointAuthorize)) {
            static::fail('Unexpected authorization object');
        }

        $result = $authorize->authorize($user, $request);

        static::assertSame(!empty($expected), $result);
        static::assertAttributeSame($expected, 'authorized', $authorize);
    }

    /**
     * Test default permissive behavior.
     *
     * @return void
     *
     * @covers ::authorize()
     * @covers ::isAnonymous()
     * @covers ::getPermissions()
     * @covers ::checkPermissions()
     */
    public function testAllowByDefault()
    {
        // Ensure no permissions apply to `/home` endpoint.
        TableRegistry::get('EndpointPermissions')->deleteAll(['role_id IS' => null]);
        TableRegistry::get('EndpointPermissions')->deleteAll(['endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => ['BEdita/API.Endpoint' => ['blockAnonymousUsers' => false]],
        ]);
        $authorize = $controller->Auth->getAuthorize('BEdita/API.Endpoint');

        if (!($authorize instanceof EndpointAuthorize)) {
            static::fail('Unexpected authorization object');
        }

        $result = $authorize->authorize(
            [
                '_anonymous' => true,
            ],
            $request
        );

        static::assertTrue($result);
        static::assertAttributeSame(true, 'authorized', $authorize);
    }

    /**
     * Test default permissive behavior on an unknown endpoint.
     *
     * @return void
     *
     * @covers ::authorize()
     * @covers ::isAnonymous()
     * @covers ::getPermissions()
     * @covers ::checkPermissions()
     */
    public function testAllowByDefaultUnknownEndpoint()
    {
        // Ensure no permissions apply to anonymous user.
        TableRegistry::get('EndpointPermissions')->deleteAll(['role_id IS' => null]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_X_API_KEY' => API_KEY,
        ];
        $uri = new Uri('/this/endpoint/definitely/doesnt/exist');
        $request = new ServerRequest(compact('environment', 'uri'));

        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => ['BEdita/API.Endpoint' => ['blockAnonymousUsers' => false]],
        ]);
        $authorize = $controller->Auth->getAuthorize('BEdita/API.Endpoint');

        if (!($authorize instanceof EndpointAuthorize)) {
            static::fail('Unexpected authorization object');
        }

        $result = $authorize->authorize(
            [
                '_anonymous' => true,
            ],
            $request
        );

        static::assertTrue($result);
        static::assertAttributeSame(true, 'authorized', $authorize);
    }

    /**
     * Test default block of anonymous writes on an endpoint unless explicitly allowed.
     *
     * @return void
     *
     * @covers ::authorize()
     * @covers ::isAnonymous()
     * @covers ::getPermissions()
     * @covers ::checkPermissions()
     * @covers ::unauthenticated()
     * @expectedException \Cake\Network\Exception\UnauthorizedException
     * @expectedExceptionMessage Unauthorized
     */
    public function testBlockAnonymousWritesByDefault()
    {
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        TableRegistry::get('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'POST',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => ['BEdita/API.Endpoint'],
        ]);
        $authorize = $controller->Auth->getAuthorize('BEdita/API.Endpoint');

        if (!($authorize instanceof EndpointAuthorize)) {
            static::fail('Unexpected authorization object');
        }

        $authorize->authorize(
            [
                '_anonymous' => true,
            ],
            $request
        );
    }

    /**
     * Test default block of anonymous actions.
     *
     * @return void
     *
     * @covers ::authorize()
     * @covers ::isAnonymous()
     * @covers ::unauthenticated()
     * @expectedException \Cake\Network\Exception\UnauthorizedException
     * @expectedExceptionMessage Unauthorized
     */
    public function testBlockUnloggedByDefault()
    {
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        TableRegistry::get('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => ['BEdita/API.Endpoint' => ['blockAnonymousUsers' => true]],
        ]);
        $authorize = $controller->Auth->getAuthorize('BEdita/API.Endpoint');

        if (!($authorize instanceof EndpointAuthorize)) {
            static::fail('Unexpected authorization object');
        }

        $authorize->authorize(
            [
                '_anonymous' => true,
            ],
            $request
        );
    }
}
