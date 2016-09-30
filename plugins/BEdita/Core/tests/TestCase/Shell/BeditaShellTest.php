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
                ['y'],
                [],
                ['pippo', 'pippo']
            ],
            'nada' => [
                [],
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

        $yesNo = array_merge($yesNo, array_fill(0, 3 - count($yesNo), 'n'));

        $mapChoice = [
            ['Proceed with database schema and data initialization?', ['y', 'n'], 'n', $yesNo[0]],
            ['Proceed with setup?', ['y', 'n'], 'n', $yesNo[1]],
            ['Overwrite current admin user?', ['y', 'n'], 'n', $yesNo[2]]
        ];

        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $dbConfig = array_merge($dbConfig, array_fill(0, 4 - count($dbConfig), ''));
        $userPass = array_merge($userPass, array_fill(0, 2 - count($userPass), ''));

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

        $this->BeditaShell->setup();

        if ($userPass[0]) {
            $usersTable = TableRegistry::get('Users');
            $user = $usersTable->get(1);
            $this->assertFalse($user->blocked);
            $this->assertEquals($userPass[0], $user->username);
        }

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
