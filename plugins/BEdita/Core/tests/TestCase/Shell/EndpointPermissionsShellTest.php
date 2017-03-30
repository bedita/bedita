<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\EndpointPermissionsShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\ORM\TableRegistry;

/**
 * \BEdita\Core\Shell\EndpointPermissionsShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\EndpointPermissionsShell
 */
class EndpointPermissionsShellTest extends ShellTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
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
     * @var \BEdita\API\Shell\EndpointPermissionsShell
     */
    public $EndpointPermissionsShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->EndpointPermissionsShell = new EndpointPermissionsShell($this->io);
        $this->EndpointPermissionsShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EndpointPermissionsShell);

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
        $parser = $this->EndpointPermissionsShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(3, $subCommands); // create, ls, rm
    }

    /**
     * Data provider for `testCreate` test case.
     *
     * @return array
     */
    public function createProvider()
    {
        return [
            'super' => [
                'true', 'true', ['application' => 'First app', 'endpoint' => 'auth', 'role' => 'first role']
            ],
            'none' => [
                'false', 'false', ['application' => 'First app', 'endpoint' => 'auth', 'role' => 'first role']
            ],
            'readonly' => [
                'true', 'false', ['application' => 'First app', 'endpoint' => 'auth', 'role' => 'first role']
            ],
            'mine' => [
                'mine', 'mine', ['application' => 'First app', 'endpoint' => 'auth', 'role' => 'first role']
            ]
        ];
    }

    /**
     * Test create method
     *
     * @param string $read permission: can be 'true', 'false', 'block', 'mine'
     * @param string $write permission: can be 'true', 'false', 'block', 'mine'
     * @param array $params shell script params
     * @return void
     *
     * @dataProvider createProvider
     * @covers ::create()
     */
    public function testCreate($read, $write, $params)
    {
        $this->EndpointPermissionsShell->params = $params;
        $this->EndpointPermissionsShell->create($read, $write);
        $id = $this->endpointPermissionsIdByReadWrite($read, $write);
        $this->assertTrue(!empty($id));
    }

    /**
     * Data provider for `testRm` test case.
     *
     * @return array
     */
    public function idsProvider()
    {
        return [
            'super' => [
                1
            ],
            'none' => [
                2
            ],
            'readonly' => [
                3
            ],
            'mine' => [
                4
            ]
        ];
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
        $expected = TableRegistry::get('EndpointPermissions')->find()->count();
        $test = $this->EndpointPermissionsShell->ls();
        $result = count($test);
        $this->assertEquals($result, $expected);
    }

    /**
     * Test rm method
     *
     * @param int $id endpoint permissions identifier
     * @return void
     *
     * @dataProvider idsProvider
     * @covers ::rm()
     */
    public function testRm($id)
    {
        if ($this->endpointPermissionsExists($id)) {
            $this->EndpointPermissionsShell->rm($id);
            $this->assertFalse($this->endpointPermissionsExists($id));
        }
    }

    /**
     * Return endpoints permission id find by $read and $write perms
     *
     * @param string $read permission: can be 'true', 'false', 'block', 'mine'
     * @param string $write permission: can be 'true', 'false', 'block', 'mine'
     * @return int $id endpoints permission identifier
     */
    private function endpointPermissionsIdByReadWrite($read, $write)
    {
        $entity = TableRegistry::get('EndpointPermissions')->newEntity();
        $entity->read = $read;
        $entity->write = $write;
        $last = TableRegistry::get('EndpointPermissions')->find()->last();
        if (($entity->encode($read) === $entity->encode($last->read)) && ($entity->encode($write) === $entity->encode($last->write))) {
            return $last->id;
        }

        return null;
    }

    /**
     * Verify endpoint existence by $id identifier
     *
     * @param int $id endpoint identifier
     * @return bool true if endpoint exists, false otherwise
     */
    private function endpointPermissionsExists($id)
    {
        return !empty(TableRegistry::get('EndpointPermissions')->find()->where(['id' => $id])->first());
    }
}
