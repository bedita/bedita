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
 * @covers \BEdita\Core\Shell\BeditaShell
 */
class BeditaShellTest extends ShellTestCase
{

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BeditaShell);

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
     * Test setup method
     *
     * @return void
     */
    public function testSetup()
    {
        $this->fixtureManager->shutDown();

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $mapChoice = [
            ['Proceed with database creation?', ['y', 'n'], 'n', 'y'],
            ['Overwrite current admin user?', ['y', 'n'], 'n', 'y'],
            ['Would you like to seed your database with an initial set of data?', ['y', 'n'], 'y', 'y'],
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['username: ', null, 'pippo'],
            ['password: ', null, 'pippo']
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

        $this->invoke(['bedita', 'setup'], [], $io);

        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->get(1);

        $this->assertFalse($user->blocked);
        $this->assertEquals('pippo', $user->username);
    }
}
