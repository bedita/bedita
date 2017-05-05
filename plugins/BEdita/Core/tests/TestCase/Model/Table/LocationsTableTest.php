<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\Database;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\LocationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\LocationsTable
 */
class LocationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\LocationsTable
     */
    public $Locations;

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
        'plugin.BEdita/Core.locations'
    ];

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
        unset($this->Locations);

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
     */
    public function testFindGeo($conditions, $numExpected)
    {
        if (!Database::supportedVersion(['vendor' => 'mysql', 'version' => '5.7'])) {
            static::expectException('BEdita\Core\Exception\BadFilterException');
        }

        $result = $this->Locations->find('geo', $conditions)->toArray();

        static::assertEquals($numExpected, count($result));
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
     */
    public function testBadGeo($conditions)
    {
        $this->Locations->find('geo', $conditions)->toArray();
    }

    /**
     * Test geo db support fail.
     *
     * @return void
     * @expectedException \BEdita\Core\Exception\BadFilterException
     *
     * @covers ::checkGeoDbSupport()
     */
    public function testBadGeoDb()
    {
        $prop = new \ReflectionProperty(get_class($this->Locations), 'geoDbSupport');
        $prop->setAccessible(true);
        $prop->setValue($this->Locations, ['vendor' => 'unknowndb']);

        $this->Locations->checkGeoDbSupport();
    }
}
