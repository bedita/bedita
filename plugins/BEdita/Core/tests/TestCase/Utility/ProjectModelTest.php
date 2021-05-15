<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\ProjectModel;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\ProjectModel} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\ProjectModel
 */
class ProjectModelTest extends TestCase
{
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
     * Test `generate()` method
     *
     * @return void
     *
     * @covers ::generate()
     * @covers ::propertyTypes()
     * @covers ::objectTypes()
     * @covers ::relations()
     * @covers ::properties()
     */
    public function testGenerate(): void
    {
        $result = ProjectModel::generate();
        static::assertNotEmpty($result);
        $expected = [
            'property_types' => 1,
            'object_types' => 10,
            'relations' => 3,
            'properties' => 10,
        ];
        static::assertEquals(array_keys($expected), array_keys($result));
        foreach ($result as $key => $val) {
            static::assertNotEmpty($val);
            static::assertCount($expected[$key], $val);
        }
    }
}
