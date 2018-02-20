<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\Marshaller;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Marshaller
 */
class MarshallerTest extends TestCase
{
    use FakeAnimalsTrait;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->setupTables();
        $this->setupAssociations();
    }

    /**
     * Test marshall data using table without inheritance
     *
     * @return void
     *
     * @covers ::_buildPropertyMap()
     * @covers ::buildTablePropertyMap()
     */
    public function testBuildPropertyMapWithoutInheritance()
    {
        $tableOptions = $this->tableOptions + ['table' => 'fake_felines'];
        $table = TableRegistry::get('FakeTiger', $tableOptions);
        $marshaller = new Marshaller($table);
        $data = [
            'name' => 'tiger',
            'legs' => 4,
            'updated_at' => '2018-02-20 12:05:00',
        ];

        $entity = $marshaller->one($data);
        static::assertEquals($data, $entity->extract($entity->visibleProperties()));
    }

    /**
     * Data provider for testBuildPropertyMap()
     *
     * @return array
     */
    public function buildPropertyMapProvider()
    {
        return [
            'marshallInheritedDate' => [
                [
                    // fake_animals table fields
                    'name' => 'tiger',
                    'legs' => 4,
                    'updated_at' => '2018-02-20 12:05:00',
                    // fake_felines table fields
                    'family' => 'The big tiger family',
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'updated_at' => new Time('2018-02-20 12:05:00'),
                    'family' => 'The big tiger family',
                ]
            ],
            'marshallInheritedEmptyDate' => [
                [
                    // fake_animals table fields
                    'name' => 'tiger',
                    'legs' => 4,
                    'updated_at' => '',
                    // fake_felines table fields
                    'family' => 'The big tiger family',
                ],
                [
                    'name' => 'tiger',
                    'legs' => 4,
                    'updated_at' => null,
                    'family' => 'The big tiger family',
                ]
            ],
        ];
    }

    /**
     * Test marshall data with inheritance
     *
     * @param array $data The data to be marshalled
     * @param array $expected The array of entity visible properties
     * @return void
     *
     * @dataProvider buildPropertyMapProvider()
     * @covers ::_buildPropertyMap()
     * @covers ::buildTablePropertyMap()
     */
    public function testBuildPropertyMap(array $data, array $expected)
    {
        $marshaller = new Marshaller($this->fakeFelines);
        $entity = $marshaller->one($data);

        $entity = $marshaller->one($data);
        static::assertEquals($expected, $entity->extract($entity->visibleProperties()));
    }
}
