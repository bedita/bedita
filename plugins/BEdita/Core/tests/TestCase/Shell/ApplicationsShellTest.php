<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\ApplicationsShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\ORM\TableRegistry;

/**
 * \BEdita\Core\Shell\ApplicationsShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\ApplicationsShell
 */
class ApplicationsShellTest extends ShellTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.applications',
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
     * @var \BEdita\API\Shell\ApplicationsShell
     */
    public $ApplicationsShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->ApplicationsShell = new ApplicationsShell($this->io);
        $this->ApplicationsShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApplicationsShell);

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
        $parser = $this->ApplicationsShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(6, $subCommands); // create, disable, enable, ls, renew_token, rm
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
                'dummyWithDesc', 'This is the description for the dummy app',
            ]
        ];
    }

    /**
     * Test create method
     *
     * @param string $name application name
     * @param string $description application description
     * @return void
     *
     * @dataProvider createProvider
     * @covers ::create()
     */
    public function testCreate($name, $description = null)
    {
        $this->ApplicationsShell->create($name, $description);
        $id = $this->applicationIdByName($name);
        $this->assertTrue(!empty($id));
    }

    /**
     * Data provider for `testEnable`,  `testDisable`, `testRenewToken`, `testRm` test cases.
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
     * @param int $id application identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::enable()
     * @covers ::modifyApplication()
     */
    public function testEnable($id)
    {
        if ($this->applicationExists($id)) {
            // enable by id
            $this->ApplicationsShell->enable($id);
            $this->assertTrue($this->applicationEnabled($id));
            // enable by name
            $name = $this->applicationNameById($id);
            $this->ApplicationsShell->enable($name);
            $this->assertTrue($this->applicationEnabled($id));
        }
    }

    /**
     * Test disable method
     *
     * @param int $id application identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::disable()
     * @covers ::modifyApplication()
     */
    public function testDisable($id)
    {
        if ($this->applicationExists($id)) {
            // disable by id
            $this->ApplicationsShell->disable($id);
            $this->assertFalse($this->applicationEnabled($id));
            // disable by name
            $name = $this->applicationNameById($id);
            $this->ApplicationsShell->disable($name);
            $this->assertFalse($this->applicationEnabled($id));
        }
    }

    /**
     * Test renewToken method
     *
     * @param int $id application identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::renewToken()
     * @covers ::modifyApplication()
     */
    public function testRenewToken($id)
    {
        if ($this->applicationExists($id)) {
            $apiKey = $this->applicationApiKey($id);
            $this->ApplicationsShell->renewToken($id);
            $apiKeyRenew = $this->applicationApiKey($id);
            $this->assertTrue(!empty($apiKeyRenew));
            $this->assertTrue(is_string($apiKeyRenew));
            $this->assertNotEquals($apiKey, $apiKeyRenew);
            // renew token by name
            $apiKey = $apiKeyRenew;
            $name = $this->applicationNameById($id);
            $this->ApplicationsShell->renewToken($name);
            $apiKeyRenew = $this->applicationApiKey($id);
            $this->assertTrue(!empty($apiKeyRenew));
            $this->assertTrue(is_string($apiKeyRenew));
            $this->assertNotEquals($apiKey, $apiKeyRenew);
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
        $expected = TableRegistry::get('Applications')->find()->count();
        $test = $this->ApplicationsShell->ls();
        $result = count($test);
        $this->assertEquals($result, $expected);
    }

    /**
     * Test rm method
     *
     * @param int $id application identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::rm()
     */
    public function testRm($id)
    {
        if ($this->applicationExists($id)) {
            $this->ApplicationsShell->rm($id);
            $this->assertFalse($this->applicationExists($id));
        }
    }

    /**
     * Return application id find by $name name
     *
     * @param string $name application name
     * @return int $id application identifier
     */
    private function applicationIdByName($name)
    {
        return TableRegistry::get('Applications')->find()->where(['name' => $name])->firstOrFail()->id;
    }

    /**
     * Return application name find by $id application identifier
     *
     * @param int $id application identifier
     * @return string application name
     */
    private function applicationNameById($id)
    {
        return TableRegistry::get('Applications')->find()->where(['id' => $id])->firstOrFail()->name;
    }

    /**
     * Verify application existence by $id identifier
     *
     * @param int $id application identifier
     * @return bool true if application exists, false otherwise
     */
    private function applicationExists($id)
    {
        return !empty(TableRegistry::get('Applications')->find()->where(['id' => $id])->first());
    }

    /**
     * Verify application is enabled, by $id identifier
     *
     * @param int $id application identifier
     * @return bool true if application is enabled, false otherwise
     */
    private function applicationEnabled($id)
    {
        $enabled = TableRegistry::get('Applications')->find()->where(['id' => $id])->firstOrFail()->enabled;

        return ($enabled === true);
    }

    /**
     * Return application api_key by $id identifier
     *
     * @param int $id application identifier
     * @return string api key
     */
    private function applicationApiKey($id)
    {
        return TableRegistry::get('Applications')->find()->where(['id' => $id])->firstOrFail()->api_key;
    }
}
