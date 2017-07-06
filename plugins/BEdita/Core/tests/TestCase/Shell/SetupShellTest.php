<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\BeditaShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Database\Connection;
use Cake\Database\Schema\Table;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * \BEdita\Core\Shell\BeditaShell Test Case
 *
 * @covers \BEdita\Core\Shell\BeditaShell
 */
class SetupShellTest extends ShellTestCase
{

    /**
     * Temporary test connection name.
     *
     * @var string
     */
    const CONNECTION_NAME = 'test_tmp';

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();

        ConnectionManager::setConfig(static::CONNECTION_NAME, $this->fakeDbParams());
        ConnectionManager::alias(static::CONNECTION_NAME, 'default');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->dropTables('default');

        ConnectionManager::alias('test', 'default');

        $this->dropTables('default');

        ConnectionManager::drop(static::CONNECTION_NAME);
        ConnectionManager::drop(BeditaShell::TEMP_SETUP_CFG);

        parent::tearDown();
    }

    protected function dropTables($config)
    {
        $defaultConnection = ConnectionManager::get($config);
        if ($defaultConnection instanceof Connection && $defaultConnection->isConnected()) {
            $defaultConnection
                ->disableConstraints(function (Connection $connection) {
                    $tables = $connection->getSchemaCollection()->listTables();

                    foreach ($tables as $table) {
                        $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                        foreach ($sql as $query) {
                            $connection->query($query);
                        }
                    }
                });
        }
    }

    /**
     * Get parameters for fake DB configuration.
     *
     * @return array
     */
    protected function fakeDbParams()
    {
        $fakeParams = [
            'className' => 'Cake\Database\Connection',
            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
            'database' => '__BE4_DB_DATABASE__',
        ];
        $fakeParams = array_merge(ConnectionManager::get('test', false)->config(), $fakeParams);

        return $fakeParams;
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
        ConnectionManager::alias('test', 'default');

        $info = ConnectionManager::get('default')->config();
        if (strstr($info['driver'], 'Sqlite') !== false) {
            $this->markTestSkipped('Initial setup does not yet support SQLite');
        }

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
            $dbConfig = array_fill(0, 5, '');
        }
        if (empty($userPass)) {
            $userPass = array_fill(0, 2, '');
        }

        $map = [
            ['Host?', 'localhost', $dbConfig[0]],
            ['Port?', '3306', $dbConfig[1]],
            ['Database?', null, $dbConfig[2]],
            ['Username?', null, $dbConfig[3]],
            ['Password?', null, $dbConfig[4]],
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
        $config = ConnectionManager::get('test', false)->config();
        $driver = str_replace('Cake\Database\Driver\\', '', $config['driver']);
        if (strstr($config['driver'], 'Sqlite') !== false) {
            $this->markTestSkipped('Initial setup does not yet support SQLite');
        }

        $fakeParams = array_merge(
            $config,
            $this->fakeDbParams()
        );

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $map = [
            ['Host?', 'localhost', $config['host']],
            ['Port?', ($driver === 'Mysql') ? '3306' : '5432', $fakeParams['port']],
            ['Database?', null, $fakeParams['database']],
            ['Username?', null, $fakeParams['username']],
            ['Password?', null, $fakeParams['password']],
        ];
        $io->method('ask')
            ->will($this->returnValueMap($map));

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);

        $mapChoice = [
            ['Driver?', ['Mysql', 'Postgres', 'Sqlite'], 'Mysql', $driver],
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
            ->will($this->returnValueMap($mapChoice));

        $res = $this->invoke(['bedita', 'setup'], [], $io);
        $this->assertFalse($res);
    }

    /**
     * Data provider fot `testFake3` test case.
     *
     * @return array
     */
    public function fake3Provider()
    {
        return [
            'success' => [
                true,
                function ($file) {
                    copy(CONFIG . 'app.default.php', $file);
                },
            ],
            'missing' => [
                false,
                function ($file) {
                    if (!file_exists($file)) {
                        return;
                    }

                    unlink($file);
                },
            ],
            'empty' => [
                false,
                function ($file) {
                    touch($file);
                },
            ],
        ];
    }

    /**
     * Test save connection to file failure
     *
     * @param bool $success Expected success.
     * @param callable $callback Callback to be invoked on temporary config file.
     * @return void
     *
     * @dataProvider fake3Provider
     */
    public function testFake3($success, callable $callback)
    {
        $config = ConnectionManager::get('test', false)->config();
        $driver = str_replace('Cake\Database\Driver\\', '', $config['driver']);
        if (strstr($config['driver'], 'Mysql') === false) {
            $this->markTestSkipped('Initial setup does not yet support SQLite nor PostgreSQL');
        }

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Driver?', ['Mysql', 'Postgres', 'Sqlite'], 'Mysql', $driver],
            ['Proceed with setup?', ['y', 'n'], 'n', 'y'],
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', 'y'],
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['Host?', 'localhost', isset($config['host']) ? $config['host'] : ''],
            ['Port?', '3306', isset($config['port']) ? $config['port'] : ''],
            ['Database?', null, isset($config['database']) ? $config['database'] : ''],
            ['Username?', null, isset($config['username']) ? $config['username'] : ''],
            ['Password?', null, isset($config['password']) ? $config['password'] : ''],
            ['username: ', null, 'username'],
            ['password: ', null, '42']
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

        $fakeCfg = TMP . '_test_app.php.tmp';
        $callback($fakeCfg);

        $res = $this->invoke(['bedita', 'setup', '--config-file', $fakeCfg], [], $io);
        $this->assertNotAborted();
        $this->assertEquals($success, $res);
    }

    /**
     * Test init schema failure
     *
     * @return void
     */
    public function testInitSchemaFailure()
    {
        ConnectionManager::alias('test', 'default');

        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

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
     * Data provider for `testAdminUserFailure` test case.
     *
     * @return array
     */
    public function adminUserFailureProvider()
    {
        return [
            'success' => [
                true,
                false,
                'gustavo',
            ],
            // 'failure' => [
            //     null,
            //     true,
            //     'bedita',
            // ],
        ];
    }

    /**
     * Test admin user setup failure
     *
     * @param bool $success Expected success.
     * @param bool $aborted Expected shell abortion.
     * @param string $username Default username.
     * @return void
     * @dataProvider adminUserFailureProvider
     */
    public function testAdminUserFailure($success, $aborted, $username)
    {
        ConnectionManager::alias('test', 'default');

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

        BeditaShell::$defaultUsername = $username;
        $res = $this->invoke(['bedita', 'setup'], [], $io);
        if ($success !== null) {
            $this->assertEquals($success, $res);
        }
        if ($aborted) {
            $this->assertAborted();
        } else {
            $this->assertNotAborted();
        }
    }
}
