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

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Identifier\JwtSubjectIdentifier;
use Authentication\Identity;
use BEdita\API\Middleware\ApplicationMiddleware;
use BEdita\API\Test\Utility\TestRequestHandler;
use BEdita\API\Utility\JWTHandler;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * {@see \BEdita\API\Middleware\ApplicationMiddleware} Test Case
 *
 * @coversDefaultClass \BEdita\API\Middleware\ApplicationMiddleware
 */
class ApplicationMiddlewareTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * Previously app set.
     *
     * @var \BEdita\Core\Model\Entity\Application
     */
    protected $prevApp = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->prevApp = CurrentApplication::getApplication();
        CurrentApplication::setApplication();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        CurrentApplication::setApplication($this->prevApp);
    }

    /**
     * Helper method to generate JWT tokens.
     *
     * @param array $user The user data
     * @param \BEdita\Core\Model\Entity\Application $app The application entity
     * @return array
     */
    protected function generateJwtTokens(array $user, Application $app): array
    {
        $prevApp = CurrentApplication::getApplication();
        CurrentApplication::setApplication($app); // done to build JWT with `app` claim
        $tokens = JWTHandler::tokens($user, 'https://example.com');
        CurrentApplication::setApplication($prevApp);

        return $tokens;
    }

    /**
     * Helper class for get mock of AuthenticationService.
     *
     * @param \Authentication\Identity|null $identity The identity
     * @param \Authentication\Authenticator\AbstractAuthenticator|null $authenticator The success authenticator
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForAuthService(?Identity $identity = null, ?AbstractAuthenticator $authenticator = null): MockObject
    {
        $serviceMock = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentity', 'getAuthenticationProvider'])
            ->getMock();

         $serviceMock
            ->method('getIdentity')
            ->willReturn($identity);

        $serviceMock
            ->method('getAuthenticationProvider')
            ->willReturn($authenticator);

        return $serviceMock;
    }

    /**
     * Test what happens with no authentication service.
     *
     * @return void
     * @covers ::process()
     */
    public function testInvalidService(): void
    {
        $request = new ServerRequest();
        $handler = new TestRequestHandler();
        $middleware = new ApplicationMiddleware();
        $middleware->process($request, $handler);

        static::assertNotInstanceOf(AuthenticationServiceInterface::class, $handler->request->getAttribute('authentication'));
        static::assertNull(CurrentApplication::getApplication());
    }

    /**
     * Test that if identity contains an Application instance
     * then middleware set it as current app (client credentials flow).
     *
     * @return void
     * @covers ::process()
     */
    public function testApplicationAsIdentity(): void
    {
        static::assertNull(CurrentApplication::getApplication());

        $expected = $this->fetchTable('Applications')->find('apiKey', ['apiKey' => API_KEY])->firstOrFail();
        $serviceMock = $this->getMockForAuthService(new Identity($expected));
        $request = (new ServerRequest())->withAttribute('authentication', $serviceMock);
        $handler = new TestRequestHandler();

        $middleware = new ApplicationMiddleware();
        $middleware->process($request, $handler);

        static::assertSame($expected, CurrentApplication::getApplication());
    }

    /**
     * Data provider for `testAppFromJWT()`.
     *
     * @return array
     */
    public function appFromJWTProvider(): array
    {
        return [
            'read from jwt already decoded => user identity found' => [
                1,
                ['id' => 1],
                new JwtAuthenticator(new JwtSubjectIdentifier(), ['subjectKey' => 'id']),
                false,
            ],
            'read from jwt not decoded => user identity not found' => [
                1,
                [],
                null,
                false,
            ],
            'refresh token with user => check application too' => [
                1,
                ['id' => 1],
                new JwtAuthenticator(new JwtSubjectIdentifier(), ['tokenField' => 'id']),
                true,
            ],
        ];
    }

    /**
     * Test reading application from JWT.
     *
     * @param int $expected The app id expected
     * @param array $user The user data for jwt
     * @param \Authentication\Authenticator\AbstractAuthenticator|null $authProvider Authenticator used for extract identity from JWT
     * @param bool $refreshToken If it is expected a refresh token request
     * @return void
     * @covers ::process()
     * @covers ::readPayload()
     * @covers ::readApplication()
     * @covers ::setupFromPayload()
     * @covers ::applicationFromPayload()
     * @dataProvider appFromJWTProvider()
     */
    public function testAppFromJWT(int $expected, array $user, ?AbstractAuthenticator $authProvider, bool $refreshToken): void
    {
        $expectedApp = $this->fetchTable('Applications')->find()->where(['id' => $expected])->firstOrFail();
        $extractKey = $refreshToken ? 'renew' : 'jwt';
        $jwt = Hash::get($this->generateJwtTokens($user, $expectedApp), $extractKey);

        static::assertNull(CurrentApplication::getApplication());

        $request = new ServerRequest([
            'environment' => [
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $jwt),
            ],
        ]);

        if ($refreshToken) {
            $request = $request->withData('grant_type', 'refresh_token');
        }

        if ($authProvider instanceof AbstractAuthenticator) {
            $authProvider->authenticate($request); // decode jwt setting payload
        }

        $serviceMock = $this->getMockForAuthService(null, $authProvider);
        $request = $request->withAttribute('authentication', $serviceMock);

        $handler = new TestRequestHandler();
        $middleware = new ApplicationMiddleware();
        $middleware->process($request, $handler);

        static::assertEquals($expectedApp->id, CurrentApplication::getApplicationId());
    }

    /**
     * Test that during a user refresh token if the application results disabled
     * an `\Cake\Http\Exception\UnauthorizedException` is thrown.
     *
     * @return void
     * @covers ::process()
     * @covers ::readPayload()
     * @covers ::readApplication()
     * @covers ::setupFromPayload()
     * @covers ::applicationFromPayload()
     */
    public function testAppDisabledDuringUserRefreshToken(): void
    {
        $this->expectExceptionObject(new UnauthorizedException(__('Application unauthorized')));

        $AppTable = $this->fetchTable('Applications');
        /** @var \BEdita\Core\Model\Entity\Application $app */
        $app = $AppTable->find()->where(['id' => 2])->firstOrFail(); // app 2 is disabled

        $jwt = Hash::get($this->generateJwtTokens(['id' => 1], $app), 'renew');

        $request = new ServerRequest([
            'environment' => [
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $jwt),
            ],
        ]);

        $serviceMock = $this->getMockForAuthService();
        $request = $request->withData('grant_type', 'refresh_token')
            ->withAttribute('authentication', $serviceMock);

        // before execute middleware disable the app
        $app->enabled = false;
        $AppTable->saveOrFail($app);

        $handler = new TestRequestHandler();
        $middleware = new ApplicationMiddleware();
        $middleware->process($request, $handler);
    }

    /**
     * Test that if JWT decode fails and missing API key
     * then a `\Cake\Http\Exception\ForbiddenException` is thrown
     *
     * @return void
     * @covers ::process()
     * @covers ::readPayload()
     * @covers ::readApplication()
     * @covers ::setupFromPayload()
     * @covers ::applicationFromApiKey()
     * @covers ::fetchApiKey()
     */
    public function testFailToDecodeJwtAndMissingApiKey(): void
    {
        $this->expectExceptionObject(new ForbiddenException('Missing API key'));

        $request = new ServerRequest([
            'environment' => [
                'HTTP_AUTHORIZATION' => 'Bearer not-valid',
            ],
        ]);

        $serviceMock = $this->getMockForAuthService();
        $request = $request->withAttribute('authentication', $serviceMock);

        $handler = new TestRequestHandler();
        $middleware = new ApplicationMiddleware();
        $middleware->process($request, $handler);
    }

    /**
     * Data provider for `testSetAppFromApiKey()`.
     *
     * @return array
     */
    public function setFromApiKeyProvider(): array
    {
        return [
            'ok' => [
                1,
                API_KEY,
            ],
            'missing but allowed' => [
                null,
                null,
                [
                    'blockAnonymousApps' => false,
                ],
            ],
            'missing but not allowed' => [
                new ForbiddenException('Missing API key'),
                null,
            ],
            'app not found' => [
                new ForbiddenException('Invalid API key'),
                'xyz',
            ],
        ];
    }

    /**
     * Test set application from API key.
     *
     * @param int|\Exception $expected
     * @param string $apiKey The API key
     * @param array $config The middleware conf
     * @return void
     * @covers ::process()
     * @covers ::readPayload()
     * @covers ::readApplication()
     * @covers ::setupFromPayload()
     * @covers ::applicationFromApiKey()
     * @covers ::fetchApiKey()
     * @dataProvider setFromApiKeyProvider()
     */
    public function testSetAppFromApiKey($expected, ?string $apiKey, array $config = []): void
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        static::assertNull(CurrentApplication::getApplicationId());

        $middleware = new ApplicationMiddleware($config);
        $request = new ServerRequest();
        $serviceMock = $this->getMockForAuthService();
        $request = $request->withAttribute('authentication', $serviceMock)
            ->withAddedHeader($middleware->getConfig('apiKey.header'), $apiKey);

        $handler = new TestRequestHandler();
        $middleware->process($request, $handler);

        static::assertEquals($expected, CurrentApplication::getApplicationId());

        // again but with query string
        CurrentApplication::setApplication();
        $request = $request->withoutHeader('X-Api-Key')
            ->withQueryParams([$middleware->getConfig('apiKey.query') => $apiKey]);

        $middleware->process($request, $handler);

        static::assertEquals($expected, CurrentApplication::getApplicationId());
    }
}
