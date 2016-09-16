<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\BeditaShell;
use BEdita\Core\Utility\Database;
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

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BeditaShell);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     */
    public function testGetOptionParser()
    {
        $parser = $this->BeditaShell->getOptionParser();
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

        $mapChoice = [
            ['Proceed with database creation?', ['y', 'n'], 'n', 'y'],
            ['Overwrite current admin user?', ['y', 'n'], 'n', 'y'],
        ];

        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $map = [
            ['username: ', null, 'pippo'],
            ['password: ', null, 'pippo']
        ];
        $this->io->method('ask')
             ->will($this->returnValueMap($map));

        $info = Database::basicInfo();
        if ($info['vendor'] != 'mysql') {
            // TODO: BeditaShell::init works only in MySQL
            return;
        }

        $this->BeditaShell->setup();

        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->get(1);

        $this->assertEquals(1, $user->id);
        $this->assertFalse($user->blocked);
        $this->assertEquals('pippo', $user->username);

        $schema = Database::currentSchema();
        if (!empty($schema)) {
            $res = Database::executeTransaction($this->dropTablesSql($schema));
            $this->assertNotEmpty($res);
            $this->assertEquals($res['success'], true);
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
            $sql[] = 'DROP TABLE IF EXISTS ' . $k;
        }
        $sql[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return $sql;
    }
}
