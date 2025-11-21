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
use ArrayObject;
use Authentication\Identifier\Resolver\ResolverInterface;
use BEdita\API\Identifier\ApplicationIdentifier;
use Cake\Core\InstanceConfigTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\API\Identifier\ApplicationIdentifier} Test Case.
 */
#[CoversClass(ApplicationIdentifier::class)]
class ApplicationIdentifierTest extends TestCase
{
    /**
     * Test `identify` method
     *
     * @return void
     */
    public function testIdentify(): void
    {
        $app = new ArrayObject([
            'client_id' => 'gustavo',
            'client_secret' => 'segreto',
        ]);

        $resolver = new class ($app) implements ResolverInterface {
            use InstanceConfigTrait;

            protected array $_defaultConfig = [];

            public function __construct(protected ArrayObject $app)
            {
            }

            public function find(array $conditions, string $type = self::TYPE_AND): ArrayAccess|array|null
            {
                return $this->app;
            }
        };

        $identifier = new ApplicationIdentifier();
        $identifier->setResolver($resolver);

        $result = $identifier->identify([
            'client_id' => 'gustavo',
            'client_secret' => 'segreto',
        ]);

        static::assertEquals($app, $result);
    }
}
