<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Policy;

use Authorization\Policy\Exception\MissingPolicyException;
use BEdita\API\Policy\ObjectPolicy;
use BEdita\API\Policy\ObjectResolver;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Entity\Role;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Policy\ObjectResolver} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Policy\ObjectResolver
 */
class ObjectResolverTest extends TestCase
{
    /**
     * Data provider for `testGetPolicy()`
     *
     * @return array
     */
    public function getPolicyProvider(): array
    {
        $notFoundResource = new Role();

        return [
            'found' => [
                ObjectPolicy::class,
                new ObjectEntity(),
            ],
            'not found' => [
                new MissingPolicyException($notFoundResource),
                $notFoundResource,
            ],
        ];
    }

    /**
     * Test `getPolicy()`.
     *
     * @param string| $expected
     * @param mixed $resource
     * @return void
     * @covers ::getPolicy()
     * @dataProvider getPolicyProvider
     */
    public function testGetPolicy($expected, $resource): void
    {
        if ($expected instanceof MissingPolicyException) {
            $this->expectExceptionObject($expected);
        }

        $resolver = new ObjectResolver();
        $actual = $resolver->getPolicy($resource);
        static::assertInstanceOf($expected, $actual);
    }
}
