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

use BEdita\Core\Model\Entity\Role;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Role} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Role
 */
class RoleTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.roles',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get('Roles');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Roles);

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
        $role = $this->Roles->get(1);

        $data = [
            'id' => 42,
            'backend_auth' => 0,
            'name' => 'patched_role_name',
        ];
        $role = $this->Roles->patchEntity($role, $data);
        if (!($role instanceof Role)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $role->id);
        $this->assertTrue($role->unchangeable);
        $this->assertEquals('patched_role_name', $role->name);
    }
}
