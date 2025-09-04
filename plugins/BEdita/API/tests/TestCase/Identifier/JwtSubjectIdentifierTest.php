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
use BEdita\API\Identifier\JwtSubjectIdentifier;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\API\Identifier\JwtSubjectIdentifier} Test Case.
 */
#[CoversClass(JwtSubjectIdentifier::class)]
class JwtSubjectIdentifierTest extends TestCase
{
    /**
     * Test `identify` method
     *
     * @return void
     */
    public function testIdentify(): void
    {
        $resolver = $this->getMockBuilder(ResolverInterface::class)
            ->onlyMethods(['find'])
            ->getMock();

        $resolver->method('find')
            ->willReturn([]);

        $identifier = new JwtSubjectIdentifier();
        $identifier->setResolver($resolver);

        $result = $identifier->identify([]);
        static::assertNull($result);

        $result = $identifier->identify([
            'sub' => 'gustavo',
        ]);
        static::assertEquals([], $result);
    }
}
