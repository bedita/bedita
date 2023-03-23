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

namespace BEdita\API\Test\TestCase\Identifier;

use BEdita\API\Identifier\OTPIdentifier;
use BEdita\Core\State\CurrentApplication;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Identifier\OTPIdentifier} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Identifier\OTPIdentifier
 */
class OTPIdentifierTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.UserTokens',
    ];

    /**
     * Data provider for `testIdentify`.
     *
     * @return array
     */
    public function identifyProvider(): array
    {
        return [
            'request ok' => [
                [
                    'authorization_code',
                ],
                [
                    'username' => 'first user',
                    'otp' => 'request',
                    'auth_provider' => 'otp',
                ],
            ],
            'request fail' => [
                null,
                [
                    'user' => 'first user',
                    'otp' => 'request',
                ],
            ],
            'request no user' => [
                null,
                [
                    'auth_provider' => 'otp',
                    'otp' => 'request',
                ],
            ],
            'request no otp param' => [
                null,
                [
                    'username' => 'somebody',
                ],
            ],
            'request wrong user' => [
                null,
                [
                    'username' => 'somebody',
                    'otp' => 'request',
                    'auth_provider' => 'otp',
                ],
            ],
            'access wrong user' => [
                null,
                [
                    'username' => 'somebody',
                    'authorization_code' => 'toktoktoktoktok',
                    'token' => 'secretsecretsecret',
                    'otp' => 'access',
                    'auth_provider' => 'otp',
                ],
            ],
            'access ok' => [
                [
                    'id',
                    'username',
                ],
                [
                    'username' => 'second user',
                    'authorization_code' => 'toktoktoktoktok',
                    'token' => 'secretsecretsecret',
                    'otp' => 'access',
                    'auth_provider' => 'otp',
                ],
            ],
            'access fail' => [
                null,
                [
                    'username' => 'second user',
                    'authorization_code' => '123123123123',
                    'token' => '123123123123',
                    'otp' => 'access',
                    'auth_provider' => 'otp',
                ],
            ],
            'access fail no code' => [
                null,
                [
                    'username' => 'second user',
                    'token' => '123123123123',
                    'otp' => 'access',
                    'auth_provider' => 'otp',
                ],
            ],
        ];
    }

    /**
     * Test `identify` method.
     *
     * @param array|null $expected Expected result.
     * @param array $credentials Request.
     * @return void
     * @dataProvider identifyProvider
     * @covers ::identify()
     * @covers ::otpAccess()
     * @covers ::otpRequest()
     */
    public function testIdentify(?array $expected, array $credentials): void
    {
        CurrentApplication::setApplication($this->fetchTable('Applications')->get(1));

        $auth = new OTPIdentifier();
        $result = $auth->identify($credentials);

        if (is_array($expected)) {
            foreach ($expected as $key) {
                static::assertArrayHasKey($key, $result);
            }
        } else {
            static::assertSame($expected, $result);
        }
    }

    /**
     * Test secret token generation
     *
     * @return void
     * @covers ::generateSecretToken()
     * @covers ::defaultSecretGenerator()
     */
    public function testGenerateSecret()
    {
        $identifier = new OTPIdentifier();
        $result = $identifier->generateSecretToken();
        static::assertNotEmpty($result);
        static::assertSame(6, strlen($result));
    }

    /**
     * Test custom secret token generation
     *
     * @return void
     * @covers ::generateSecretToken()
     */
    public function testGenerateSecretCustom()
    {
        $identifier = new OTPIdentifier();
        $identifier->setConfig(['generator' => 'time']);
        $result = $identifier->generateSecretToken();
        static::assertNotEmpty($result);
        static::assertSame(10, strlen($result));
    }
}
