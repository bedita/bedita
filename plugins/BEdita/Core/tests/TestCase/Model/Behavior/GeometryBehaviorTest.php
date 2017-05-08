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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Utility\Database;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\GeometryBehavior
 */
class GeometryBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.locations',
    ];

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\LocationsTable
     */
    public $Locations;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Locations = TableRegistry::get('Locations');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Geometry);

        parent::tearDown();
    }

    /**
     * Data provider for `testFindGeo` test case.
     *
     * @return array
     */
    public function findGeoProvider()
    {
        return [
            'near point' => [
                [
                    'center' => '44.4944876,11.3464721',
                ],
                1,
            ],
            'near array' => [
                [
                    'center' => [44.4944183, 11.3464055],
                ],
                1,
            ],
            'near array with integer' => [
                [
                    'center' => [44, 11.3464055],
                ],
                1,
            ],
            'near array with radius' => [
                [
                    'center' => [45.4944183, 12.3464055],
                    'radius' => 1,
                ],
                0,
            ],
        ];
    }

    /**
     * Test findGeo finder method.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     *
     * @dataProvider findGeoProvider
     * @covers ::findGeo()
     * @covers ::checkGeoSupport()
     * @covers ::getDistanceExpression()
     * @covers ::parseCoordinates()
     */
    public function testFindGeo($conditions, $numExpected)
    {
        $supported = Database::supportedVersion(['vendor' => 'mysql', 'version' => '5.7']);
        if (!$supported) {
            $this->expectException(BadFilterException::class);
        }

        $result = $this->Locations->find('geo', $conditions)->toArray();

        if ($supported) {
            static::assertEquals($numExpected, count($result));
        } else {
            static::fail('This backend is not supposed to have geometric types support');
        }
    }

    /**
     * Data provider for `testBadGeo` test case.
     *
     * @return array
     */
    public function badGeoProvider()
    {
        return [
            'gustavo' => [
                [
                    'gustavo' => '44.4944876,11.3464721',
                ],
            ],
            'not geo' => [
                [
                    'center' => ['somewhere', 11.3464055],
                ],
            ],
            'not a hypersphere' => [
                [
                    'center' => [-5.54645654, 11.3464055, 12.5645745],
                ],
            ],
            'out of range lat' => [
                [
                    'center' => [200, 0],
                ],
            ],
            'out of range long' => [
                [
                    'center' => [0, 100],
                ],
            ],
        ];
    }

    /**
     * Test finder error.
     *
     * @param array $conditions Filter options.
     * @return void
     * @expectedException \BEdita\Core\Exception\BadFilterException
     *
     * @dataProvider badGeoProvider
     * @covers ::findGeo()
     * @covers ::parseCoordinates()
     */
    public function testBadGeo($conditions)
    {
        $this->Locations->find('geo', $conditions)->toArray();
    }
}
