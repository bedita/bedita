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

use BEdita\API\Identifier\OAuth2Identifier;
use Cake\Http\Client\Adapter\Stream;
use Cake\Http\Client\Response;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Identifier\OAuth2Identifier} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Identifier\OAuth2Identifier
 */
class OAuth2IdentifierTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.ObjectTypes',
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
            'found' => [
                [
                    'id' => 1,
                ],
                [
                    'auth_provider' => 'example',
                    'provider_username' => 'first_user',
                    'access_token' => 'very-log-string',
                ],
                [
                    'owner_id' => 'first_user',
                    'other' => 'lot of stolen data',
                ],
            ],
            'not found' => [
                null,
                [
                    'auth_provider' => 'example',
                    'provider_username' => 'another_user',
                    'access_token' => 'very-log-string',
                ],
                [
                    'owner_id' => 'another_user',
                    'other' => 'lot of useless data',
                ],
            ],
            'no provider' => [
                null,
                [
                    'auth_provider' => 'linkedout',
                    'provider_username' => 'someone',
                    'access_token' => 'very-log-string',
                ],
            ],
            'no auth' => [
                null,
                [
                    'auth_provider' => 'example',
                    'provider_username' => 'another_user',
                    'access_token' => 'very-log-string',
                ],
                [
                    'owner_id' => 'first_user',
                    'other' => 'lot of useless data',
                ],
            ],
            'missing' => [
                null,
                [
                    'auth_provider' => 'example',
                    'provider_username' => '',
                    'access_token' => '',
                ],
            ],
        ];
    }

    /**
     * Test `identify` method.
     *
     * @param array|null $expected Expected result.
     * @param array $credentials Request.
     * @param array $oauthResponse OAuth2 server response.
     * @return void
     * @dataProvider identifyProvider
     * @covers ::identify()
     * @covers ::getOAuth2Response()
     */
    public function testIdentify(?array $expected, array $credentials, array $oauthResponse = []): void
    {
        $authProvider = $this->fetchTable('AuthProviders')->find()
            ->where(['name' => $credentials['auth_provider']])
            ->firstOrFail();
        // Mock OAuth2 response
        $response = new Response([], json_encode($oauthResponse));
        $mock = $this->getMockBuilder(Stream::class)
            ->getMock();
        $mock->method('send')
            ->willReturn([$response]);

        $params = (array)$authProvider->get('params');
        $params['options'] = ['client' => ['adapter' => $mock, 'protocolVersion' => '2']];
        $authProvider->set(compact('params'));

        $identifier = new OAuth2Identifier();
        $identifier->setConfig(compact('authProvider'));

        $result = $identifier->identify($credentials);

        if (is_array($expected)) {
            $result = array_intersect_key($result->toArray(), $expected);
        }
        static::assertSame($expected, $result);
    }
}
