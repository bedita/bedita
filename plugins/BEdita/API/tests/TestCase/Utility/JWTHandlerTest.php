<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Utility;

use BEdita\API\Utility\JWTHandler;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @coversDefaultClass \BEdita\API\Utility\JWTHandler
 */
class JWTHandlerTest extends TestCase
{
    /**
     * Data provider for `testAuthenticate` test case.
     *
     * @return array
     */
    public function decodeProvider(): array
    {
        $payload = ['someData' => 'someValue'];
        $token = JWT::encode($payload, Security::getSalt(), 'HS256');
        $invalidToken = 'gustavo';
        $expiredToken = JWT::encode(['exp' => time() - 10], Security::getSalt(), 'HS256');

        return [
            'default' => [
                $payload,
                $token,
            ],
            'invalidToken' => [
                new \UnexpectedValueException('Wrong number of segments'),
                $invalidToken,
            ],
            'expiredToken' => [
                new \Firebase\JWT\ExpiredException('Expired token'),
                $expiredToken,
            ],
            'wrongAlgorithmOption' => [
                new \InvalidArgumentException('Algorithm must be a string'),
                $token,
                [
                    'algorithm' => ['HS256'],
                ],
            ],
        ];
    }

    /**
     * Test `decode` method.
     *
     * @param array|false|\Exception $expected Expected result.
     * @param string $token Token.
     * @param array $options Decode options.
     * @return void
     * @dataProvider decodeProvider
     * @covers ::decode()
     */
    public function testDecode($expected, string $token, array $options = []): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
            $this->expectExceptionCode($expected->getCode());
        }

        $result = JWTHandler::decode($token, $options);
        static::assertEquals($expected, $result);
    }

    /**
     * Test `tokens` method.
     *
     * @return void
     * @covers ::tokens()
     * @covers ::applicationData()
     */
    public function testTokens(): void
    {
        $user = [
            'id' => 99,
            'username' => 'gustavo',
        ];

        $tokens = JWTHandler::tokens($user, 'http://api.example.org');

        static::assertArrayHasKey('jwt', $tokens);
        static::assertArrayHasKey('renew', $tokens);

        $jwt = (array)JWT::decode($tokens['jwt'], new Key(Security::getSalt(), 'HS256'));

        static::assertArrayHasKey('iat', $jwt);
        static::assertArrayHasKey('nbf', $jwt);
        static::assertArrayHasKey('exp', $jwt);

        unset($jwt['iat'], $jwt['nbf'], $jwt['exp']);
        $expected = $user + [
            'iss' => Router::fullBaseUrl(),
            'app' => null,
        ];
        static::assertEquals($expected, $jwt);

        $renew = (array)JWT::decode($tokens['renew'], new Key(Security::getSalt(), 'HS256'));

        static::assertArrayHasKey('iat', $renew);
        static::assertArrayHasKey('nbf', $renew);
        static::assertArrayNotHasKey('exp', $renew);

        $expected = [
            'iss' => Router::fullBaseUrl(),
            'app' => null,
            'sub' => 99,
            'aud' => 'http://api.example.org',
        ];
        unset($renew['iat'], $renew['nbf'], $renew['exp']);
        static::assertEquals($expected, $renew);
    }

    /**
     * Test `applicationData` method.
     *
     * @return void
     * @covers ::applicationData()
     */
    public function testApplicationData(): void
    {
        $app = ['id' => 99, 'name' => 'test'];
        CurrentApplication::setApplication(new Application($app));
        $tokens = JWTHandler::tokens([], 'http://api.example.org');

        $jwt = (array)JWT::decode($tokens['jwt'], new Key(Security::getSalt(), 'HS256'));
        static::assertEquals($app, (array)$jwt['app']);

        CurrentApplication::setApplication(null);
    }
}
