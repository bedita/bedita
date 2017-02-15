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
}
