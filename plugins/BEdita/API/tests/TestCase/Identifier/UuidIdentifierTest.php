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

use ArrayAccess;
use Authentication\Identifier\Resolver\ResolverInterface;
use BEdita\API\Identifier\UuidIdentifier;
use Cake\Core\InstanceConfigTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\API\Identifier\UuidIdentifier} Test Case.
 */
#[CoversClass(UuidIdentifier::class)]
class UuidIdentifierTest extends TestCase
{
    /**
     * Fixtures.
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
    ];

    /**
     * Data provider for `testIdentify`
     *
     * @return array
     */
    public static function identifyProvider(): array
    {
        return [
            'first' => [
                ['name' => 'gustavo'],
                ['name' => 'gustavo'],
            ],
            'second' => [
                ['name' => 'gustavo'],
                null,
                ['name' => 'gustavo'],
            ],
        ];
    }

    /**
     * Test `identify` method
     *
     * @param array $expected Expected result
     * @param array|null $find1 First find
     * @param array|null $find2 Second find
     * @return void
     */
    #[DataProvider('identifyProvider')]
    public function testIdentify(array $expected, ?array $find1, ?array $find2 = null): void
    {
        $resolver = new class ($find1, $find2) implements ResolverInterface {
            use InstanceConfigTrait;

            protected array $_defaultConfig = [];

            public function __construct(protected ?array $find1, protected ?array $find2 = null)
            {
            }

            public function find(array $conditions, string $type = self::TYPE_AND): ArrayAccess|array|null
            {
                $firstCall = $this->getConfig('find1');
                if ($firstCall === null) {
                    $this->setConfig('find1', 'done');

                    return $this->find1;
                }

                return $this->find2;
            }
        };

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
