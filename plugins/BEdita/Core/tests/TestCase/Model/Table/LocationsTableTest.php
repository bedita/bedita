<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\LocationsTable;
use BEdita\Core\Utility\Database;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class TestLocations extends LocationsTable
{
    public function setGeoDbSupport($options)
    {
        $this->geoDbSupport = $options;
    }
}

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
     * Fake db params for geo test
     *
     * @var array
     */
    public $fakeDbParams = null;

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

        if (!empty($this->fakeDbParams)) {
            ConnectionManager::alias('test', 'default');
            ConnectionManager::drop('__fake__');
            $this->fakeDbParams = null;
        }

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
            'nearPoint' => [
                [
                    'center' => '44.4944876,11.3464721',
                ],
                1,
            ],
            'nearArray' => [
                [
                    'center' => [44.4944183, 11.3464055],
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
            static::expectException('Cake\Network\Exception\BadRequestException');
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
            'notgeo' => [
                [
                    'center' => ['somewhere', 11.3464055],
                ],
            ],
        ];
    }

    /**
     * Test finder error.
     *
     * @return void
     *
     * @dataProvider badGeoProvider
     * @covers ::findGeo()
     */
    public function testBadGeo($conditions)
    {
        static::expectException('Cake\Network\Exception\BadRequestException');

        $result = $this->Locations->find('geo', $conditions)->toArray();
    }

    /**
     * Test geo db support fail.
     *
     * @return void
     *
     * @covers ::checkGeoDbSupport()
     */
    public function testBadGeoDb()
    {
        $testLocations = new TestLocations();
        $testLocations->setGeoDbSupport(['vendor' => 'unknowndb']);

        static::expectException('Cake\Network\Exception\BadRequestException');

        $result = $testLocations->checkGeoDbSupport();
    }
}
