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

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Entity\Relation;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Relation} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Relation
 */
class RelationTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\RelationsTable
     */
    public $Relations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.relations',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Relations = TableRegistry::get('Relations');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Relations);

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
        $relation = $this->Relations->get(1);

        $data = [
            'id' => 42,
            'name' => 'patched_name',
        ];
        $relation = $this->Relations->patchEntity($relation, $data);
        if (!($relation instanceof Relation)) {
            static::fail(sprintf('Unexpected entity class "%s"', get_class($relation)));
        }

        static::assertEquals(1, $relation->id);
        static::assertEquals('patched_name', $relation->name);
    }

    /**
     * Test setter method for `name` and getter method for `alias`.
     *
     * @return void
     *
     * @covers ::_setName()
     * @covers ::_getAlias()
     */
    public function testSetName()
    {
        $data = [
            'name' => 'FooBar',
        ];
        $relation = $this->Relations->newEntity($data);
        if (!($relation instanceof Relation)) {
            static::fail(sprintf('Unexpected entity class "%s"', get_class($relation)));
        }

        static::assertEquals('foo_bar', $relation->name);
        static::assertEquals('FooBar', $relation->alias);
    }

    /**
     * Test setter method for `inverse_name` and getter method for `inverse_alias`.
     *
     * @return void
     *
     * @covers ::_setInverseName()
     * @covers ::_getInverseAlias()
     */
    public function testSetInverseName()
    {
        $data = [
            'inverse_name' => 'bar-foo',
        ];
        $relation = $this->Relations->newEntity($data);
        if (!($relation instanceof Relation)) {
            static::fail(sprintf('Unexpected entity class "%s"', get_class($relation)));
        }

        static::assertEquals('bar_foo', $relation->inverse_name);
        static::assertEquals('BarFoo', $relation->inverse_alias);
    }
}
