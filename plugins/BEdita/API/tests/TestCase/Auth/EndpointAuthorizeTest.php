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
use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\UriInterface;

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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        Cache::clear(false, '_bedita_core_');
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
     * @covers ::checkPermissions()
     */
    public function testAuthorize($expected, UriInterface $uri, array $user, $requestMethod = 'GET', $whiteListed = false)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        CurrentApplication::setApplication(TableRegistry::getTableLocator()->get('Applications')->get(2));

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
     * @covers ::checkPermissions()
     */
    public function testAllowByDefault()
    {
        // Ensure no permissions apply to `/home` endpoint.
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['role_id IS' => null]);
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['endpoint_id' => 2]);

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
     * @covers ::checkPermissions()
     */
    public function testAllowByDefaultUnknownEndpoint()
    {
        // Ensure no permissions apply to anonymous user.
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['role_id IS' => null]);

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
     * @covers ::checkPermissions()
     * @covers ::unauthenticated()
     * @expectedException \Cake\Http\Exception\UnauthorizedException
     * @expectedExceptionMessage Unauthorized
     */
    public function testBlockAnonymousWritesByDefault()
    {
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

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
     * @expectedException \Cake\Http\Exception\UnauthorizedException
     * @expectedExceptionMessage Unauthorized
     */
    public function testBlockUnloggedByDefault()
    {
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

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
