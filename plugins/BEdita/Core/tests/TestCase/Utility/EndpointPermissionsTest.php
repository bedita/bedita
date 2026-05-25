<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Model\Table\EndpointPermissionsTable;
use BEdita\Core\Utility\EndpointPermissions;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Utility\EndpointPermissions} Test Case
 */
#[CoversClass(EndpointPermissions::class)]
class EndpointPermissionsTest extends TestCase
{
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
     * EndpointPermissions table instance.
     *
     * @var \BEdita\Core\Model\Table\EndpointPermissionsTable
     */
    protected EndpointPermissionsTable $EndpointPermissions;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->EndpointPermissions = $this->fetchTable('EndpointPermissions');
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
     * Test `create()` method.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $data = [
            [
                'endpoint_name' => 'auth',
                'application_name' => 'First app',
                'role_name' => 'first role',
                'read' => true,
                'write' => false,
            ],
        ];

        $result = EndpointPermissions::create($data);

        static::assertCount(1, $result);

        $saved = $this->EndpointPermissions
            ->find('resource', endpoint_name: 'auth', application_name: 'First app', role_name: 'first role')
            ->firstOrFail();

        static::assertTrue($saved->get('read'));
        static::assertFalse($saved->get('write'));
    }

    /**
     * Test `create()` with null fields (all-endpoints, all-applications, anonymous).
     *
     * @return void
     */
    public function testCreateWithNulls(): void
    {
        // Remove existing fixture row with all-null identifiers first
        $existing = $this->EndpointPermissions
            ->find('resource', endpoint_name: null, application_name: null, role_name: null)
            ->firstOrFail();
        $this->EndpointPermissions->deleteOrFail($existing);

        $data = [
            [
                'endpoint_name' => null,
                'application_name' => null,
                'role_name' => null,
                'read' => 'mine',
                'write' => 'block',
            ],
        ];

        EndpointPermissions::create($data);

        $saved = $this->EndpointPermissions
            ->find('resource', endpoint_name: null, application_name: null, role_name: null)
            ->firstOrFail();

        static::assertSame('mine', $saved->get('read'));
        static::assertSame('block', $saved->get('write'));
    }

    /**
     * Test `update()` method.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        // Fixture row 3: home / Disabled app / first role → read='mine', write='block'
        $data = [
            [
                'endpoint_name' => 'home',
                'application_name' => 'Disabled app',
                'role_name' => 'first role',
                'read' => true,
                'write' => true,
            ],
        ];

        $result = EndpointPermissions::update($data);

        static::assertCount(1, $result);

        $saved = $this->EndpointPermissions
            ->find('resource', endpoint_name: 'home', application_name: 'Disabled app', role_name: 'first role')
            ->firstOrFail();

        static::assertTrue($saved->get('read'));
        static::assertTrue($saved->get('write'));
    }

    /**
     * Test `update()` with 'mine' and 'block' string values.
     *
     * @return void
     */
    public function testUpdateWithStringValues(): void
    {
        // Fixture row 2: null / First app / null → read=true, write=true
        $data = [
            [
                'endpoint_name' => null,
                'application_name' => 'First app',
                'role_name' => null,
                'read' => 'mine',
                'write' => 'block',
            ],
        ];

        EndpointPermissions::update($data);

        $saved = $this->EndpointPermissions
            ->find('resource', endpoint_name: null, application_name: 'First app', role_name: null)
            ->firstOrFail();

        static::assertSame('mine', $saved->get('read'));
        static::assertSame('block', $saved->get('write'));
    }

    /**
     * Test `remove()` method.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $countBefore = $this->EndpointPermissions->find()->count();

        $data = [
            [
                'endpoint_name' => 'auth',
                'application_name' => 'Disabled app',
                'role_name' => 'second role',
            ],
        ];

        EndpointPermissions::remove($data);

        static::assertEquals($countBefore - 1, $this->EndpointPermissions->find()->count());

        $found = $this->EndpointPermissions
            ->find('resource', endpoint_name: 'auth', application_name: 'Disabled app', role_name: 'second role')
            ->first();
        static::assertNull($found);
    }

    /**
     * Test `remove()` with multiple items.
     *
     * @return void
     */
    public function testRemoveMultiple(): void
    {
        $data = [
            [
                'endpoint_name' => 'home',
                'application_name' => 'Disabled app',
                'role_name' => 'first role',
            ],
            [
                'endpoint_name' => 'home',
                'application_name' => 'Disabled app',
                'role_name' => 'second role',
            ],
        ];

        EndpointPermissions::remove($data);

        static::assertEquals(3, $this->EndpointPermissions->find()->count());
    }
}
