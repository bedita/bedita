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

use ArrayObject;
use Authentication\Identifier\Resolver\ResolverInterface;
use BEdita\API\Identifier\ApplicationIdentifier;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Identifier\ApplicationIdentifier} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Identifier\ApplicationIdentifier
 */
class ApplicationIdentifierTest extends TestCase
{
    /**
     * Test `identify` method
     *
     * @return void
     * @covers ::identify()
     */
    public function testIdentify(): void
    {
        $resolver = $this->getMockBuilder(ResolverInterface::class)
            ->onlyMethods(['find'])
            ->addMethods(['setConfig'])
            ->getMock();

        $app = new ArrayObject([
            'client_id' => 'gustavo',
            'client_secret' => 'segreto',
        ]);

        $resolver->method('find')
            ->willReturn($app);
        $resolver->method('setConfig')
            ->willReturn([]);

        $identifier = new ApplicationIdentifier();
        $identifier->setResolver($resolver);

        $result = $identifier->identify([
            'client_id' => 'gustavo',
            'client_secret' => 'segreto',
        ]);

        static::assertEquals($app, $result);
    }
}
