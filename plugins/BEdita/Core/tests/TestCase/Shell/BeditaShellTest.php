<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\BeditaShell;
use BEdita\Core\Utility\Database;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Shell\BeditaShell Test Case
 *
 * @covers \BEdita\Core\Shell\BeditaShell
 */
class BeditaShellTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \BEdita\Core\Shell\BeditaShell
     */
    public $BeditaShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
        'plugin.BEdita/Core.users',
    ];

    /**
     * @inheritDoc
     */
    public $autoFixtures = false;

    /**
     * Exclude from drop tables action
     */
    public $excludeFromDrop = [];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->BeditaShell = new BeditaShell($this->io);
        $this->BeditaShell->initialize();
    }

    protected function fakeDbSetup($configName, $prefix = '__BE4_', $suffix = '__')
    {
        $fake = [
            'className' => 'Cake\Database\Connection',
            'host' => $prefix . 'DB_HOST' . $suffix,
            'username' => $prefix . 'DB_USERNAME' . $suffix,
            'password' => $prefix . 'DB_PASSWORD' . $suffix,
            'database' => $prefix . 'DB_DATABASE' . $suffix,
        ];
        $info = Database::basicInfo();
        $fake = array_merge($info, $fake);
        Configure::write('Datasources.' . $configName, $fake);
        ConnectionManager::config($configName, $fake);

        return $fake;
    }


    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->dbCleanup();
        unset($this->BeditaShell);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     * @coversNothing
     */
    public function testGetOptionParser()
    {
        $parser = $this->BeditaShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(1, $subCommands);
        $this->assertArrayHasKey('setup', $subCommands);
    }

    public function setupInputProvider()
    {
        return [
            'noSetup' => [
                ['y', 'n', 'y'],
                [],
                ['pippo', 'pippo']
            ],
            'nada' => [
                ['n', 'n', 'n'],
            ]
        ];
    }

    /**
     * Test setup method
     *
     * @return void
     * @dataProvider setupInputProvider
     */
    public function testSetup($yesNo, $dbConfig = [], $userPass = [])
    {
        $this->fixtureManager->shutDown();

        $mapChoice = [
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', $yesNo[0]],
            ['Proceed with setup?', ['y', 'n'], 'n', $yesNo[1]],
            ['Overwrite current admin user?', ['y', 'n'], 'n', $yesNo[2]]
        ];

        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        if (empty($dbConfig)) {
            $dbConfig = array_fill(0, 4, '');
        }
        if (empty($userPass)) {
            $userPass = array_fill(0, 2, '');
        }

        $map = [
            ['Host?', null, $dbConfig[0]],
            ['Database?', null, $dbConfig[1]],
            ['Username?', null, $dbConfig[2]],
            ['Password?', null, $dbConfig[3]],
            ['username: ', null, $userPass[0]],
            ['password: ', null, $userPass[1]]
        ];

        $this->io->method('ask')
             ->will($this->returnValueMap($map));

        $info = Database::basicInfo();
        if ($info['vendor'] != 'mysql') {
            $this->markTestSkipped('MySQL only supported (for now)');
        }

        $res = $this->BeditaShell->setup();

        if ($userPass[0]) {
            $usersTable = TableRegistry::get('Users');
            $user = $usersTable->get(1);
            $this->assertFalse($user->blocked);
            $this->assertEquals($userPass[0], $user->username);
        }

        $this->dbCleanup();
    }

    /**
     * Test setup with fake connection data
     *
     * @return void
     */
    public function testFake()
    {
        $this->fixtureManager->shutDown();

        $this->fakeDbSetup('__test-temp__');
        ConnectionManager::alias('__test-temp__', 'default');

        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);

        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop('__test-temp__');
        $this->dbCleanup();
    }

    /**
     * Test setup with fake data
     *
     * @return void
     */
    public function testFake2()
    {
        $this->fixtureManager->shutDown();

        $fakeParams = $this->fakeDbSetup('__test-temp__');
        ConnectionManager::alias('__test-temp__', 'default');

        $map = [
            ['Host?', null, $fakeParams['host']],
            ['Database?', null, $fakeParams['database']],
            ['Username?', null, $fakeParams['username']],
            ['Password?', null, $fakeParams['password']],
        ];
        $this->io->method('ask')
             ->will($this->returnValueMap($map));

        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
        ];
        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);


        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop('__test-temp__');
        $this->dbCleanup();
    }


    /**
     * Test save connection to file failure
     *
     * @return void
     */
    public function testFake3()
    {
        $this->fixtureManager->shutDown();

        $info = Database::basicInfo();

        $fakeParams = $this->fakeDbSetup('__test-temp__');
        ConnectionManager::alias('__test-temp__', 'default');

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'y'],
        ];
        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['Host?', null, $info['host']],
            ['Database?', null, $info['database']],
            ['Username?', null, $info['username']],
            ['Password?', null, $info['password']],
            ['username: ', null, 'username'],
            ['password: ', null, '42']
        ];
        $this->io->method('ask')
             ->will($this->returnValueMap($map));

        $fakeCfg = TMP . '_test_app.php.tmp';

        $res = copy(CONFIG . 'app.default.php', $fakeCfg);
        $this->assertTrue($res);
        $this->BeditaShell->configPath = $fakeCfg;

        $res = $this->BeditaShell->setup();
        $this->assertTrue($res);

        $res = unlink($fakeCfg);
        $this->assertTrue($res);
        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);

        $res = touch($fakeCfg);
        $this->assertTrue($res);
        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);

        $this->dbCleanup();

        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop('__test-temp__');
    }


    /**
     * Test init schema failure
     *
     * @return void
     */
    public function testInitSchemaFailure()
    {
        $this->fixtureManager->shutDown();
        $this->loadFixtures('Config');

        $info = Database::basicInfo();

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
        ];
        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $res = $this->BeditaShell->setup();
        $this->assertFalse($res);

        $this->excludeFromDrop[] = 'config';
        $this->dbCleanup();
    }

    /**
     * Test admin user setup failure
     *
     * @return void
     */
    public function testAdminUserFailure()
    {
        $this->fixtureManager->shutDown();

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'y'],
        ];
        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $info = Database::basicInfo();
        $map = [
            ['username: ', null, ''],
            ['password: ', null, '']
        ];
        $this->io->method('ask')
             ->will($this->returnValueMap($map));

        $this->BeditaShell->defaultUsername = 'somename';
        $res = $this->BeditaShell->setup();
        $this->assertTrue($res);

        $this->BeditaShell->defaultUsername = 'bedita';
        $this->expectException('\Cake\Console\Exception\StopException');
        $this->BeditaShell->setup();

    }


    protected function dbCleanup()
    {
        $schema = Database::currentSchema();
        if (!empty($schema)) {
            $res = Database::executeTransaction($this->dropTablesSql($schema));
            $this->assertNotEmpty($res);
            $this->assertEquals($res['success'], true);
        }
        if (ConnectionManager::config('__temp_setup__')) {
            ConnectionManager::drop('__temp_setup__');
        }
    }

    /**
     * Returns SQL DROP statements to empty DB
     *
     * @param array $schema DB schema metadata
     * @return array SQL drop statements
     */
    protected function dropTablesSql($schema)
    {
        $sql[] = 'SET FOREIGN_KEY_CHECKS=0;';
        foreach ($schema as $k => $v) {
            if (!in_array($k, $this->excludeFromDrop)) {
                $sql[] = 'DROP TABLE IF EXISTS ' . $k;
            }
        }
        $sql[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return $sql;
    }
}
