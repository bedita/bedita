<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Model\Entity\Endpoint;
use BEdita\Core\Model\Entity\EndpointPermission;
use BEdita\Core\Model\Entity\Role;
use BEdita\Core\Model\Table\EndpointPermissionsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Entity\EndpointPermission} Test Case
 *
 * @since 4.0.0
 */
#[CoversClass(EndpointPermission::class)]
class EndpointPermissionTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\EndpointPermissionsTable
     */
    public EndpointPermissionsTable $EndpointPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->EndpointPermissions = TableRegistry::getTableLocator()->get('EndpointPermissions');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->EndpointPermissions);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     */
    public function testAccessible()
    {
        $endpointPermission = $this->EndpointPermissions->get(1);

        $data = [
            'id' => 42,
        ];
        $endpointPermission = $this->EndpointPermissions->patchEntity($endpointPermission, $data);
        if (!($endpointPermission instanceof EndpointPermission)) {
            throw new InvalidArgumentException();
        }

        $this->assertEquals(1, $endpointPermission->id);
    }

    /**
     * Data provider for `testEncode` test case.
     *
     * @return array
     */
    public static function encodeProvider(): array
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
     */
    #[DataProvider('encodeProvider')]
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
    public static function decodeProvider(): array
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
     */
    #[DataProvider('decodeProvider')]
    public function testDecode($expected, $encoded)
    {
        $decoded = EndpointPermission::decode($encoded);

        static::assertSame($expected, $decoded);
    }

    /**
     * Data provider for `testSetPermission` test case.
     *
     * @return array
     */
    public static function setPermissionProvider(): array
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
     * Test magic setter for permission.
     *
     * @param int $expected Expected permission value.
     * @param mixed $permission Permission to set.
     * @param int $initial Initial value.
     * @return void
     */
    #[DataProvider('setPermissionProvider')]
    public function testSetPermission($expected, $permission, $initial = 0)
    {
        $entity = new EndpointPermission([
            'permission' => $initial,
        ]);

        $entity->set('permission', $permission);

        static::assertEquals($expected, $entity->get('permission'));
    }

    /**
     * Data provider for `testGetReadWrite` test case.
     *
     * @return array
     */
    public static function getReadWriteProvider(): array
    {
        return [
            'readOnly' => [
                [
                    'read' => true,
                    'write' => false,
                ],
                0b0011,
            ],
            'block' => [
                [
                    'read' => 'block',
                    'write' => 'block',
                ],
                0b1010,
            ],
            'writeOnly' => [
                [
                    'read' => false,
                    'write' => 'mine',
                ],
                0b0100,
            ],
        ];
    }

    /**
     * Test magic getters for read and write permissions.
     *
     * @param array $expected Expected decoded values.
     * @param int $permission Permission.
     * @return void
     */
    #[DataProvider('getReadWriteProvider')]
    public function testGetReadWrite(array $expected, $permission)
    {
        $entity = new EndpointPermission(compact('permission'));

        $read = $entity->get('read');
        $write = $entity->get('write');

        static::assertSame($expected['read'], $read);
        static::assertSame($expected['write'], $write);
    }

    /**
     * Data provider for `testSetRead` and `testSetWrite` test cases.
     *
     * @return array
     */
    public static function setReadWriteProvider(): array
    {
        return [
            'no' => [
                false,
                0b1111,
            ],
            'mine' => [
                'mine',
            ],
            'block' => [
                'block',
                0b1111,
            ],
            'yes' => [
                'true',
                0,
                true,
            ],
            'invalid' => [
                'invalid',
                0b1111,
                false,
            ],
        ];
    }

    /**
     * Test magic setter for read permission.
     *
     * @param string|bool $read Read permission.
     * @param int $permission Initial permission value.
     * @param string|bool|null $expected Expected read permission value (if `null`, same as `$read`).
     * @return void
     */
    #[DataProvider('setReadWriteProvider')]
    public function testSetRead($read, $permission = 0, $expected = null)
    {
        if ($expected === null) {
            $expected = $read;
        }

        $entity = new EndpointPermission(compact('permission'));

        $entity->set('read', $read);
        $read = $entity->get('read');

        static::assertSame($expected, $read);
    }

    /**
     * Test magic setter for write permission.
     *
     * @param string|bool $write Write permission.
     * @param int $permission Initial permission value.
     * @param string|bool|null $expected Expected write permission value (if `null`, same as `$write`).
     * @return void
     */
    #[DataProvider('setReadWriteProvider')]
    public function testSetWrite($write, $permission = 0, $expected = null)
    {
        if ($expected === null) {
            $expected = $write;
        }

        $entity = new EndpointPermission(compact('permission'));

        $entity->set('write', $write);
        $write = $entity->get('write');

        static::assertSame($expected, $write);
    }

    /**
     * Data provder for `testSetEndpointName()`
     *
     * @return array
     */
    public static function setEndpointNameProvider(): array
    {
        return [
            'null' => [
                null,
                null,
            ],
            'valid name' => [
                2,
                'home',
            ],
            'not valid name' => [
                new RecordNotFoundException('Record not found in table `endpoints`'),
                'dontfindme',
            ],
        ];
    }

    /**
     * Test magic setter for endpoint_name.
     *
     * @param mixed $expected The expected data
     * @param string $name The endpoint name
     * @return void
     */
    #[DataProvider('setEndpointNameProvider')]
    public function testSetEndpointName($expected, ?string $name): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(RecordNotFoundException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $entity = new EndpointPermission();
        $entity->set('endpoint_name', $name);
        $endpoint = $entity->endpoint;
        if ($expected === null) {
            static::assertNull($endpoint);
            static::assertNull($entity->endpoint_id);

            return;
        }

        static::assertInstanceOf(Endpoint::class, $endpoint);
        static::assertEquals($expected, $endpoint->id);
        static::assertEquals($expected, $entity->endpoint_id);
    }

    /**
     * Data provder for `testSetRoleName()`
     *
     * @return array
     */
    public static function setRoleNameProvider(): array
    {
        return [
            'null' => [
                null,
                null,
            ],
            'valid name' => [
                2,
                'second role',
            ],
            'not valid name' => [
                new RecordNotFoundException('Record not found in table `roles`'),
                'dontfindme',
            ],
        ];
    }

    /**
     * Test magic setter for role_name.
     *
     * @param mixed $expected The expected data
     * @param string $name The role name
     * @return void
     */
    #[DataProvider('setRoleNameProvider')]
    public function testSetRoleName($expected, ?string $name): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(RecordNotFoundException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $entity = new EndpointPermission();
        $entity->set('role_name', $name);
        $role = $entity->role;
        if ($expected === null) {
            static::assertNull($role);
            static::assertNull($entity->role_id);

            return;
        }

        static::assertInstanceOf(Role::class, $role);
        static::assertEquals($expected, $role->id);
        static::assertEquals($expected, $entity->role_id);
    }

    /**
     * Data provder for `testSetApplicationName()`
     *
     * @return array
     */
    public static function setApplicationNameProvider(): array
    {
        return [
            'null' => [
                null,
                null,
            ],
            'valid name' => [
                1,
                'First app',
            ],
            'not valid name' => [
                new RecordNotFoundException('Record not found in table `applications`'),
                'dontfindme',
            ],
        ];
    }

    /**
     * Test magic setter for application_name.
     *
     * @param mixed $expected The expected data
     * @param string $name The application name
     * @return void
     */
    #[DataProvider('setApplicationNameProvider')]
    public function testSetApplicationName($expected, ?string $name): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(RecordNotFoundException::class);
            $this->expectExceptionMessage($expected->getMessage());
        }

        $entity = new EndpointPermission();
        $entity->set('application_name', $name);
        $application = $entity->application;
        if ($expected === null) {
            static::assertNull($application);
            static::assertNull($entity->application_id);

            return;
        }

        static::assertInstanceOf(Application::class, $application);
        static::assertEquals($expected, $application->id);
        static::assertEquals($expected, $entity->application_id);
    }
}
