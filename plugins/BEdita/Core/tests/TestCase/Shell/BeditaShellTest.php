<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\BeditaShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * \BEdita\Core\Shell\BeditaShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\BeditaShell
 */
class BeditaShellTest extends ShellTestCase
{

    /**
     * Temporary test connection name.
     *
     * @var string
     */
    const CONNECTION_NAME = 'test_tmp';

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
    ];

    /**
     * {@inheritDoc}
     */
    public $autoFixtures = false;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop(self::CONNECTION_NAME);
        ConnectionManager::drop(BeditaShell::TEMP_SETUP_CFG);

        $this->fixtureManager->shutDown();

        ConnectionManager::get('default')
            ->disableConstraints(function (Connection $connection) {
                $tables = $connection->schemaCollection()->listTables();

                foreach ($tables as $table) {
                    $sql = $connection->schemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->query($query);
                    }
                }
            });

        parent::tearDown();
    }

    /**
     * Set up a fake DB configuration.
     *
     * @param string $configName Database config name.
     * @return array
     */
    protected function fakeDbSetup($configName = self::CONNECTION_NAME)
    {
        $fake = [
            'className' => 'Cake\Database\Connection',
            'host' => '__BE4_DB_HOST__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
            'database' => '__BE4_DB_DATABASE__',
        ];

        ConnectionManager::config($configName, array_merge(ConnectionManager::get('default')->config(), $fake));
        ConnectionManager::alias($configName, 'default');

        return $fake;
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     * @coversNothing
     */
    public function testGetOptionParser()
    {
        $parser = (new BeditaShell())->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(1, $subCommands);
        $this->assertArrayHasKey('setup', $subCommands);
    }

    /**
     * Data provider for `testSetup` test case.
     *
     * @return array
     */
    public function setupProvider()
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
     * @param array $yesNo Answers to "y/n" questions.
     * @param array $dbConfig Answers to db config questions.
     * @param array $userPass Answers to user creation questions.
     * @return void
     *
     * @dataProvider setupProvider
     */
    public function testSetup(array $yesNo, array $dbConfig = [], array $userPass = [])
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', $yesNo[0]],
            ['Proceed with setup?', ['y', 'n'], 'n', $yesNo[1]],
            ['Overwrite current admin user?', ['y', 'n'], 'n', $yesNo[2]],
            ['Would you like to seed your database with an initial set of data?', ['y', 'n'], 'y', 'n'],
        ];
        $io->method('askChoice')
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
            ['password: ', null, $userPass[1]],
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

        $res = $this->invoke(['bedita', 'setup'], [], $io);

        if ($yesNo[2] === 'y') {
            $this->assertNotAborted();
            $this->assertTrue($res);

            $user = TableRegistry::get('Users')->get(1);
            $this->assertFalse($user->blocked);
            $this->assertEquals($userPass[0], $user->username);
        } else {
            $this->assertFalse($res);
        }
    }

    /**
     * Test setup with fake connection data
     *
     * @return void
     */
    public function testFake()
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'n'],
            ['Proceed with setup?', ['y', 'n'], 'n', 'n'],
        ];
        $io->method('askChoice')
            ->will($this->returnValueMap($mapChoice));

        $this->fakeDbSetup();

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);
    }

    /**
     * Test setup with fake data
     *
     * @return void
     */
    public function testFake2()
    {
        $fakeParams = $this->fakeDbSetup();

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $map = [
            ['Host?', null, $fakeParams['host']],
            ['Database?', null, $fakeParams['database']],
            ['Username?', null, $fakeParams['username']],
            ['Password?', null, $fakeParams['password']],
        ];
        $io->method('ask')
            ->will($this->returnValueMap($map));

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
            ->will($this->returnValueMap($mapChoice));

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);
    }

    /**
     * Test save connection to file failure
     *
     * @return void
     */
    public function testFake3()
    {
        $config = ConnectionManager::get('default')->config();

        $this->fakeDbSetup();

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['Host?', null, isset($config['host']) ? $config['host'] : ''],
            ['Database?', null, isset($config['database']) ? $config['database'] : ''],
            ['Username?', null, isset($config['username']) ? $config['username'] : ''],
            ['Password?', null, isset($config['password']) ? $config['password'] : ''],
            ['username: ', null, 'username'],
            ['password: ', null, '42']
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

        $fakeCfg = TMP . '_test_app.php.tmp';
        copy(CONFIG . 'app.default.php', $fakeCfg);
//        $this->BeditaShell->configPath = $fakeCfg;

        $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertNotAborted();
//        $this->assertTrue($res);

        unlink($fakeCfg);
        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);

        touch($fakeCfg);
        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);
    }

    /**
     * Test init schema failure
     *
     * @return void
     */
    public function testInitSchemaFailure()
    {
        $this->loadFixtures('Config');

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);
    }

    /**
     * Test admin user setup failure
     *
     * @return void
     */
    public function testAdminUserFailure()
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['username: ', null, ''],
            ['password: ', null, ''],
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

//        $this->BeditaShell->defaultUsername = 'somename';
        $this->invoke(['bedita', 'setup'], [], $io);

//        $this->BeditaShell->defaultUsername = 'bedita';
        $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertAborted();
    }
}
