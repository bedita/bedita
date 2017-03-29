<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\EndpointsShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\ORM\TableRegistry;

/**
 * \BEdita\Core\Shell\EndpointsShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\EndpointsShell
 */
class EndpointsShellTest extends ShellTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.endpoints',
    ];

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \BEdita\API\Shell\EndpointsShell
     */
    public $EndpointsShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->EndpointsShell = new EndpointsShell($this->io);
        $this->EndpointsShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EndpointsShell);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     *
     * @coversNothing
     */
    public function testGetOptionParser()
    {
        $parser = $this->EndpointsShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(5, $subCommands); // create, disable, enable, ls, rm
    }

    /**
     * Data provider for `testCreate` test case.
     *
     * @return array
     */
    public function createProvider()
    {
        return [
            'noDescription' => [
                'dummy'
            ],
            'description' => [
                'dummyWithDesc', 'This is the description for the dummy endpoint',
            ]
        ];
    }

    /**
     * Test create method
     *
     * @param string $name endpoint name
     * @param string $description endpoint description
     * @return void
     *
     * @dataProvider createProvider
     * @covers ::create()
     */
    public function testCreate($name, $description = null)
    {
        $this->EndpointsShell->create($name, $description);
        $id = $this->endpointIdByName($name);
        $this->assertTrue(!empty($id));
    }

    /**
     * Data provider for `testEnable`,  `testDisable`, `testRm` test cases.
     *
     * @return array
     */
    public function idsProvider()
    {
        return [
            'noDescription' => [
                1
            ],
            'description' => [
                2
            ]
        ];
    }

    /**
     * Test enable method
     *
     * @param int $id endpoint identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::enable()
     * @covers ::modifyEndpoint()
     */
    public function testEnable($id)
    {
        if ($this->endpointExists($id)) {
            // enable by id
            $this->EndpointsShell->enable($id);
            $this->assertTrue($this->endpointEnabled($id));
            // enable by name
            $name = $this->endpointNameById($id);
            $this->EndpointsShell->enable($name);
            $this->assertTrue($this->endpointEnabled($id));
        }
    }

    /**
     * Test disable method
     *
     * @param int $id endpoint identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::disable()
     * @covers ::modifyEndpoint()
     */
    public function testDisable($id)
    {
        if ($this->endpointExists($id)) {
            // disable by id
            $this->EndpointsShell->disable($id);
            $this->assertFalse($this->endpointEnabled($id));
            // disable by name
            $name = $this->endpointNameById($id);
            $this->EndpointsShell->disable($name);
            $this->assertFalse($this->endpointEnabled($id));
        }
    }

    /**
     * Test ls method
     *
     * @return void
     *
     * @covers ::ls()
     */
    public function testLs()
    {
        $expected = TableRegistry::get('Endpoints')->find()->count();
        $test = $this->EndpointsShell->ls();
        $result = count($test);
        $this->assertEquals($result, $expected);
    }

    /**
     * Test rm method
     *
     * @param int $id endpoint identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::rm()
     */
    public function testRm($id)
    {
        if ($this->endpointExists($id)) {
            $this->EndpointsShell->rm($id);
            $this->assertFalse($this->endpointExists($id));
        }
    }

    /**
     * Return endpoint id find by $name name
     *
     * @param string $name endpoint name
     * @return int $id endpoint identifier
     */
    private function endpointIdByName($name)
    {
        return TableRegistry::get('Endpoints')->find()->where(['name' => $name])->firstOrFail()->id;
    }

    /**
     * Return endpoint name find by $id endpoint identifier
     *
     * @param int $id endpoint identifier
     * @return string endpoint name
     */
    private function endpointNameById($id)
    {
        return TableRegistry::get('Endpoints')->find()->where(['id' => $id])->firstOrFail()->name;
    }

    /**
     * Verify endpoint existence by $id identifier
     *
     * @param int $id endpoint identifier
     * @return bool true if endpoint exists, false otherwise
     */
    private function endpointExists($id)
    {
        return !empty(TableRegistry::get('Endpoints')->find()->where(['id' => $id])->first());
    }

    /**
     * Verify endpoint is enabled, by $id identifier
     *
     * @param int $id endpoint identifier
     * @return bool true if endpoint is enabled, false otherwise
     */
    private function endpointEnabled($id)
    {
        $enabled = TableRegistry::get('Endpoints')->find()->where(['id' => $id])->firstOrFail()->enabled;

        return ($enabled === true);
    }
}
