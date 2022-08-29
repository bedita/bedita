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

use Authentication\Identifier\Resolver\ResolverInterface;
use BEdita\API\Identifier\UuidIdentifier;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Identifier\UuidIdentifier} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Identifier\UuidIdentifier
 */
class UuidIdentifierTest extends TestCase
{
    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
    ];

    /**
     * Data provider for `testIdentify`
     *
     * @return array
     */
    public function identifyProvider(): array
    {
        return [
            'first' => [
                'gustavo',
                'gustavo',
            ],
            'second' => [
                'gustavo',
                '',
                'gustavo',
            ],
        ];
    }

    /**
     * Test `identify` method
     *
     * @param string $expected Expected result
     * @param string $find1 First string
     * @param string $find2 Second string
     * @return void
     * @dataProvider identifyProvider
     * @covers ::identify()
     */
    public function testIdentify(string $expected, string $find1, string $find2 = ''): void
    {
        $resolver = $this->getMockBuilder(ResolverInterface::class)
            ->onlyMethods(['find'])
            ->addMethods(['setConfig'])
            ->getMock();
        $resolver->method('find')
            ->willReturnOnConsecutiveCalls($find1, $find2);
        $resolver->method('setConfig')
            ->willReturn([]);

        $authProvider = $this->fetchTable('AuthProviders')
            ->find()
            ->where(['name' => 'uuid'])
            ->first();
        $identifier = new UuidIdentifier(compact('authProvider'));
        $identifier->setResolver($resolver);

        $result = $identifier->identify([
            'token' => 'gustavo',
        ]);

        static::assertEquals($expected, $result);
    }
}
