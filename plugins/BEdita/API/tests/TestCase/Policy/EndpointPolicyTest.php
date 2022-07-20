<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Policy;

use Authentication\Identity as AuthenticationIdentity;
use Authorization\AuthorizationService;
use Authorization\Identity;
use Authorization\IdentityInterface;
use Authorization\Policy\MapResolver;
use BEdita\API\Policy\EndpointPolicy;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\UriInterface;

/**
 * {@see \BEdita\API\Policy\EndpointPolicy} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Policy\EndpointPolicy
 */
class EndpointPolicyTest extends TestCase
{
    use LocatorAwareTrait;

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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::clear('_bedita_core_');
    }

    /**
     * Data provider for `testAuthorize` test case.
     *
     * @return array
     */
    public function canAccessProvider()
    {
        $service = new AuthorizationService(new MapResolver());
        $arrayIdentity = new AuthenticationIdentity([
            'roles' => [
                [
                    'id' => 1,
                ],
            ],
        ]);
        $userIdentity = new AuthenticationIdentity(new User());
        $identity = new Identity($service, $arrayIdentity);

        return [
            'GET /home (anonymous)' => [
                new UnauthorizedException('Unauthorized'),
                new Uri('/home'),
                null,
            ],
            'POST /home (role_id = 1)' => [
                false,
                new Uri('/home'),
                $identity,
                'POST',
            ],
            'GET /home (role_id = 1)' => [
                true,
                new Uri('/home'),
                $identity,
            ],
            'GET /unknown-endpoint (anonymous)' => [
                new UnauthorizedException('Unauthorized'),
                new Uri('/unknown-endpoint'),
                null,
            ],
            'GET /disabled (anonymous)' => [
                new NotFoundException('Resource not found.'),
                new Uri('/disabled'),
                null,
                'GET',
                EndpointPolicy::DEFAULT_AUTHORIZED,
            ],
            'GET /disabled (role_id = 1)' => [
                new NotFoundException('Resource not found.'),
                new Uri('/disabled'),
                new Identity($service, $userIdentity),
            ],
            'POST /signup whitelist (anonymous)' => [
                true,
                new Uri('/signup'),
                null,
                'POST',
                EndpointPolicy::DEFAULT_AUTHORIZED,
            ],
            'GET /home admin only' => [
                true,
                new Uri('/home'),
                $identity,
                'GET',
                EndpointPolicy::ADMINISTRATOR_ONLY,
            ],
        ];
    }

    /**
     * Test authorization for user.
     *
     * @param bool|\Exception $expected Expected result.
     * @param \Psr\Http\Message\UriInterface $uri Request URI.
     * @param \Authentication\IdentityInterface|null $identity Identity data.
     * @param string $requestMethod Request method.
     * @param string|null $attribute Request attribute to set.
     * @return void
     * @dataProvider canAccessProvider()
     * @covers ::canAccess()
     * @covers ::checkPermissions()
     * @covers ::getUser()
     * @covers ::isAuthorized()
     */
    public function testCanAccess(
        $expected,
        UriInterface $uri,
        ?IdentityInterface $identity,
        $requestMethod = 'GET',
        ?string $attribute = null
    ) {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        CurrentApplication::setApplication($this->fetchTable('Applications')->get(2));

        $environment = [
            'REQUEST_METHOD' => $requestMethod,
        ];
        $request = new ServerRequest(compact('environment', 'uri'));
        if ($attribute) {
            $request = $request->withAttribute($attribute, true);
        }

        $policy = new EndpointPolicy();
        $result = $policy->canAccess($identity, $request);
        static::assertSame($expected, $result);
    }

    /**
     * Test default permissive behavior.
     *
     * @return void
     * @covers ::canAccess()
     * @covers ::checkPermissions()
     */
    public function testAllowByDefault()
    {
        // Ensure no permissions apply to `/home` endpoint.
        $this->fetchTable('EndpointPermissions')->deleteAll(['role_id IS' => null]);
        $this->fetchTable('EndpointPermissions')->deleteAll(['endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $policy = new EndpointPolicy();
        $result = $policy->canAccess(null, $request);

        static::assertTrue($result);
        static::assertTrue($policy->isAuthorized());
    }

    /**
     * Test default permissive behavior on an unknown endpoint.
     *
     * @return void
     * @covers ::canAccess()
     * @covers ::checkPermissions()
     */
    public function testAllowByDefaultUnknownEndpoint()
    {
        // Ensure no permissions apply to anonymous user.
        $this->fetchTable('EndpointPermissions')->deleteAll(['role_id IS' => null]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_X_API_KEY' => API_KEY,
        ];
        $uri = new Uri('/this/endpoint/definitely/doesnt/exist');
        $request = new ServerRequest(compact('environment', 'uri'));

        $policy = new EndpointPolicy();
        $result = $policy->canAccess(null, $request);

        static::assertTrue($result);
        static::assertTrue($policy->isAuthorized());
    }

    /**
     * Test default block of anonymous writes on an endpoint unless explicitly allowed.
     *
     * @return void
     * @covers ::canAccess()
     * @covers ::checkPermissions()
     */
    public function testBlockAnonymousWritesByDefault()
    {
        $this->expectException(\Cake\Http\Exception\UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        $this->fetchTable('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'POST',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $policy = new EndpointPolicy();
        $policy->canAccess(null, $request);
    }

    /**
     * Test default block of anonymous actions.
     *
     * @return void
     * @covers ::canAccess()
     */
    public function testBlockUnloggedByDefault()
    {
        $this->expectException(\Cake\Http\Exception\UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');
        // Ensure no permissions apply to anonymous user on `/home` endpoint.
        $this->fetchTable('EndpointPermissions')->deleteAll(['role_id IS' => null, 'endpoint_id' => 2]);

        $environment = [
            'REQUEST_METHOD' => 'GET',
        ];
        $uri = new Uri('/home');
        $request = new ServerRequest(compact('environment', 'uri'));

        $policy = new EndpointPolicy();
        $policy->canAccess(null, $request);
    }
}
