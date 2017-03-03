<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\JsonApiTrait
 */
class JsonApiTraitTest extends TestCase
{

    /**
     * Helper table.
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
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
     * Test magic getter for type.
     *
     * @return void
     *
     * @covers ::_getType()
     */
    public function testGetType()
    {
        $role = $this->Roles->newEntity();

        $type = $role->type;

        static::assertSame($this->Roles->getTable(), $type);
    }

    /**
     * Test magic getter for relationships.
     *
     * @return void
     *
     * @covers ::_getRelationships()
     * @covers ::listAssociations()
     */
    public function testGetRelationships()
    {
        $expected = [
            'users',
        ];

        $role = $this->Roles->newEntity();

        $relationships = $role->relationships;

        static::assertSame($expected, $relationships);
    }

    /**
     * Test magic getter for relationships.
     *
     * @return void
     *
     * @covers ::_getRelationships()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsHidden()
    {
        $role = $this->Roles->newEntity();
        $role->setHidden(['users' => true], true);

        $relationships = $role->relationships;

        static::assertSame([], $relationships);
    }
}
