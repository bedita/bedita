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
namespace BEdita\API\Test\TestCase\Middleware;

use Authentication\AuthenticationServiceInterface;
use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Identifier\JwtSubjectIdentifier;
use Authentication\Identity;
use BEdita\API\Middleware\LoggedUserMiddleware;
use BEdita\API\Test\Utility\TestAuthHelperTrait;
use BEdita\API\Test\Utility\TestRequestHandler;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Utility\LoggedUser;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\API\Middleware\LoggedUserMiddleware} Test Case
 *
 * @coversDefaultClass \BEdita\API\Middleware\LoggedUserMiddleware
 */
class LoggedUserMiddlewareTest extends TestCase
{
    use TestAuthHelperTrait;

    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectTypes',
    ];

    /**
     * Previously user set.
     *
     * @var array
     */
    protected $prevUser = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->prevUser = LoggedUser::getUser();
        LoggedUser::resetUser();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        LoggedUser::setUser($this->prevUser);
    }

    /**
     * Test that no user was set without authentication service.
     *
     * @return void
     * @covers ::process()
     */
    public function testInvalidService(): void
    {
        $request = new ServerRequest();
        $handler = new TestRequestHandler();
        $middleware = new LoggedUserMiddleware();
        $middleware->process($request, $handler);

        static::assertNotInstanceOf(AuthenticationServiceInterface::class, $handler->request->getAttribute('authentication'));
        static::assertEmpty(LoggedUser::getUser());
    }

    /**
     * Test that no user was set with empty identity.
     *
     * @return void
     * @covers ::process()
     */
    public function testEmptyIdentity(): void
    {
        $request = new ServerRequest();
        $request = $request->withAttribute('authentication', $this->getMockForAuthenticationService());
        $handler = new TestRequestHandler();
        $middleware = new LoggedUserMiddleware();
        $middleware->process($request, $handler);

        static::assertEmpty(LoggedUser::getUser());
    }

    /**
     * Data provider for `testUnauthorized()`
     *
     * @return array
     */
    public function unauthorizedProvider(): array
    {
        return [
            'auth' => ['/auth'],
            'optout' => ['/auth/optout'],
        ];
    }

    /**
     * Test that for some path missing identity it throws a `\Cake\Http\Exception\UnauthorizedException`.
     *
     * @param string $path The path to check.
     * @return void
     * @covers ::process()
     * @dataProvider unauthorizedProvider()
     */
    public function testUnauthorized(string $path): void
    {
        $this->expectExceptionObject(new UnauthorizedException('Login request not successful'));

        $request = new ServerRequest([
            'url' => '/auth',
        ]);
        $request = $request->withAttribute('authentication', $this->getMockForAuthenticationService());
        $handler = new TestRequestHandler();
        $middleware = new LoggedUserMiddleware();
        $middleware->process($request, $handler);
    }

    /**
     * Data provider for `testSetupLoggedUser()`.
     *
     * @return array
     */
    public function setupLoggedUserProvider(): array
    {
        $user = $this->fetchTable('Users')->find()->where(['id' => 1])->firstOrFail();

        return [
            'user with entity' => [
                1,
                $user,
            ],
            'user with array' => [
                1,
                ['id' => 1, 'username' => 'gustavo'],
            ],
            'user with ArrayObject' => [
                1,
                new \ArrayObject(['id' => 1, 'username' => 'gustavo']),
            ],
            'no user with array missing username' => [
                null,
                ['id' => 1],
            ],
            'no user with ArrayObject missing username' => [
                null,
                new \ArrayObject(['id' => 1]),
            ],
            'instance of Application' => [
                null,
                new Application(),
            ],
        ];
    }

    /**
     * Test user setup.
     *
     * @param int|null $expected The expected user id
     * @param array|\ArrayObject|\BEdita\Core\Model\Entity\User $identityData The identity data.
     * @return void
     * @covers ::process()
     * @covers ::checkLoggedUser()
     * @covers ::setupLoggedUser
     * @covers ::checkPayload()
     * @dataProvider setupLoggedUserProvider()
     */
    public function testSetupLoggedUser($expected, $identityData): void
    {
        $identity = new Identity($identityData);
        $request = new ServerRequest();
        $request = $request->withAttribute('authentication', $this->getMockForAuthenticationService($identity));
        $handler = new TestRequestHandler();
        $middleware = new LoggedUserMiddleware();
        $middleware->process($request, $handler);

        static::assertEquals($expected, Hash::get(LoggedUser::getUser(), 'id'));
    }

    /**
     * Test that if refresh token action of user fails (no user data in identity but `sub` claim populated)
     * but a `\Authentication\Authenticator\JwtAuthenticator` has success (recognizing app in the refresh JWT)
     * then a `\Cake\Http\Exception\UnauthorizedException` is thrown.
     *
     * @return void
     * @covers ::process()
     * @covers ::checkLoggedUser()
     * @covers ::setupLoggedUser
     * @covers ::checkPayload()
     */
    public function testUserRefreshTokenFail(): void
    {
        $this->expectExceptionObject(new UnauthorizedException('Login request not successful'));

        $app = $this->fetchTable('Applications')->find('apiKey', ['apiKey' => API_KEY])->firstOrFail();
        $jwts = $this->generateJwtTokens(['id' => 666, 'username' => 'ovatsug'], $app);

        $refreshJwt = Hash::get($jwts, 'renew');

        $request = new ServerRequest([
            'environment' => [
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $refreshJwt),
            ],
        ]);

        // simulate that the successful authenticator recognizes the app (not the user)
        // to do so we need to decode and set payload and set the application as identity
        $authProvider = new JwtAuthenticator(new JwtSubjectIdentifier());
        $authProvider->authenticate($request); // it needs to decode and set payload
        $identity = new Identity($app);
        $request = $request->withAttribute('authentication', $this->getMockForAuthenticationService($identity, $authProvider));

        $handler = new TestRequestHandler();
        $middleware = new LoggedUserMiddleware();
        $middleware->process($request, $handler);
    }
}
