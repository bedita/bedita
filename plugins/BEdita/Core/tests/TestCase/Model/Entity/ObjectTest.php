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

use BEdita\Core\Model\Entity\Object;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Object} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Object
 */
class ObjectTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    public $Objects;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::get('Objects');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Objects);

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
        $object = $this->Objects->get(1);

        $created = $object->created;
        $modified = $object->modified;
        $published = $object->published;

        $data = [
            'id' => 42,
            'locked' => false,
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
            'published' => '2016-01-01 12:00:00',
            'created_by' => 2,
            'modified_by' => 2
        ];
        $object = $this->Objects->patchEntity($object, $data);
        if (!($object instanceof Object)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $object->id);
        $this->assertTrue($object->locked);
        $this->assertEquals(1, $object->created_by);
        $this->assertEquals(1, $object->modified_by);
        $this->assertEquals($created, $object->created);
        $this->assertEquals($modified, $object->modified);
        $this->assertEquals($published, $object->published);
    }
}
