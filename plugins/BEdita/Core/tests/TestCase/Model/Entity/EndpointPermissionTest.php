<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\EndpointPermission;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\EndpointPermission} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\EndpointPermission
 *
 * @since 4.0.0
 */
class EndpointPermissionTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\EndpointPermissionsTable
     */
    public $EndpointPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->EndpointPermissions = TableRegistry::get('EndpointPermissions');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->EndpointPermissions);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $endpointPermission = $this->EndpointPermissions->get(1);

        $data = [
            'id' => 42,
        ];
        $endpointPermission = $this->EndpointPermissions->patchEntity($endpointPermission, $data);
        if (!($endpointPermission instanceof EndpointPermission)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $endpointPermission->id);
    }

    /**
     * Data provider for `testEncode` test case.
     *
     * @return array
     */
    public function encodeProvider()
    {
        return [
            'no' => [EndpointPermission::PERM_NO, false],
            'block' => [EndpointPermission::PERM_BLOCK, 'block'],
            'mine' => [EndpointPermission::PERM_MINE, 'mine'],
            'yes' => [EndpointPermission::PERM_YES, 'true'],
            'invalid' => [EndpointPermission::PERM_NO, 'INVALID'],
        ];
    }

    /**
     * Test encoding of permission values.
     *
     * @param int $expected Encoded value.
     * @param string|bool $decoded Decoded value.
     * @return void
     *
     * @covers ::encode()
     * @dataProvider encodeProvider()
     */
    public function testEncode($expected, $decoded)
    {
        $encoded = EndpointPermission::encode($decoded);

        static::assertSame($expected, $encoded);
    }

    /**
     * Data provider for `testDecode` test case.
     *
     * @return array
     */
    public function decodeProvider()
    {
        return [
            'no' => [false, EndpointPermission::PERM_NO],
            'block' => ['block', EndpointPermission::PERM_BLOCK],
            'mine' => ['mine', EndpointPermission::PERM_MINE],
            'yes' => [true, (string)EndpointPermission::PERM_YES],
            'invalid' => [false, 'INVALID'],
        ];
    }

    /**
     * Test decoding of permission values.
     *
     * @param string|bool $expected Decoded value.
     * @param int $encoded Encoded value.
     * @return void
     *
     * @covers ::decode()
     * @dataProvider decodeProvider()
     */
    public function testDecode($expected, $encoded)
    {
        $decoded = EndpointPermission::decode($encoded);

        static::assertSame($expected, $decoded);
    }

    public function setPermissionProvider()
    {
        return [
            'integer' => [
                0b0101,
                0b0101,
                0b1111,
            ],
            'array' => [
                0b1101,
                [
                    'read' => 'mine',
                    'write' => true,
                ],
            ],
            'read' => [
                0b0101,
                [
                    'read' => 'mine',
                ],
                0b0111,
            ],
            'write' => [
                0b0111,
                [
                    'write' => 'mine',
                ],
                0b1011,
            ],
            'invalid' => [
                0,
                'invalid',
            ],
            'negative' => [
                0,
                -1,
            ],
            'decimal' => [
                0b0011,
                pi(),
            ],
            'tooHigh' => [
                0b1111,
                PHP_INT_MAX,
            ],
        ];
    }

    /**
     * @param int $expected Expected permission value.
     * @param mixed $permission Permission to set.
     * @param int $initial Initial value.
     *
     * @covers ::_setPermission()
     * @dataProvider setPermissionProvider()
     */
    public function testSetPermission($expected, $permission, $initial = 0)
    {
        $entity = new EndpointPermission([
            'permission' => $initial,
        ]);

        $entity->set('permission', $permission);

        static::assertEquals($expected, $entity->get('permission'));
    }
}
