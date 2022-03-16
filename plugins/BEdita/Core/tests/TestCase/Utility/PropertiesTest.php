<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Properties;
use Cake\Datasource\ModelAwareTrait;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\Properties} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Properties
 * @property \BEdita\Core\Model\Table\PropertiesTable $Properties
 */
class PropertiesTest extends TestCase
{
    use ModelAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
    ];

    /**
     * Test properties
     *
     * @return array
     */
    protected $properties = [
        [
            'name' => 'custom_one',
            'object' => 'documents',
            'property' => 'boolean',
            'description' => 'my custom description',
        ],
        [
            'name' => 'custom_two',
            'object' => 'documents',
            'property' => 'string',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadModel('Properties');
    }

    /**
     * Test `create` method.
     *
     * @return void
     * @covers ::create()
     * @covers ::validate()
     */
    public function testCreate(): void
    {
        Properties::create($this->properties);

        $newProperties = $this->Properties
            ->find()
            ->where(['name IN' => ['custom_one', 'custom_two']])
            ->toArray();
        static::assertEquals(2, count($newProperties));
    }

    /**
     * Test `remove` method.
     *
     * @return void
     * @covers ::remove()
     */
    public function testRemove(): void
    {
        Properties::create($this->properties);

        Properties::remove($this->properties);
        $newProperties = $this->Properties
            ->find()
            ->where(['name IN' => ['custom_one', 'custom_two']])
            ->toArray();
        static::assertEquals(0, count($newProperties));
    }

    /**
     * Test `validate` failure.
     *
     * @return void
     * @covers ::validate()
     */
    public function testValidate(): void
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Missing mandatory property data "name"');

        unset($this->properties[0]['name']);
        Properties::create($this->properties);
    }

    /**
     * Test `update` method.
     *
     * @return void
     * @covers ::update()
     */
    public function testUpdate(): void
    {
        Properties::create($this->properties);

        $this->properties[1]['property'] = 'text';
        Properties::update($this->properties);

        $newProp = $this->Properties
            ->find()
            ->where(['name' => 'custom_two'])
            ->first();
        static::assertEquals('text', $newProp->get('property_type_name'));
    }
}
