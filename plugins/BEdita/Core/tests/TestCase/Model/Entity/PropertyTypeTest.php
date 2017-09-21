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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\PropertyType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\PropertyType
 */
class PropertyTypeTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\PropertyTypesTable
     */
    public $PropertyTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->PropertyTypes = TableRegistry::get('PropertyTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->PropertyTypes);

        parent::tearDown();
    }

    /**
     * Test entity
     *
     * @return void
     * @coversNothing
     */
    public function testEntity()
    {
        $propertyType = $this->PropertyTypes->get(1);
        $this->assertEquals('string', $propertyType->name);
        $this->assertEquals(['type' => 'string'], $propertyType->params);

        $data = [
            'name' => 'othername',
        ];
        $propertyType = $this->PropertyTypes->patchEntity($propertyType, $data);
        $this->assertEquals('othername', $propertyType->name);
    }
}
