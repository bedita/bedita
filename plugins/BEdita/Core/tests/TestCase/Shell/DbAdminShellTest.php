<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\DbAdminShell;
use BEdita\Core\Utility\Database;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Shell\DbAdminShell Test Case
 */
class DbAdminShellTest extends TestCase
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
     * @var \BEdita\Core\Shell\DbAdminShell
     */
    public $DbAdminShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->DbAdminShell = new DbAdminShell($this->io);
        $this->DbAdminShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DbAdminShell);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->DbAdminShell->initialize();
        $this->assertFileExists($this->DbAdminShell->schemaDir);
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     */
    public function testGetOptionParser()
    {
        $parser = $this->DbAdminShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(3, $subCommands);
        $this->assertArrayHasKey('saveSchema', $subCommands);
        $this->assertArrayHasKey('checkSchema', $subCommands);
        $this->assertArrayHasKey('init', $subCommands);
    }

    /**
     * Test saveSchema method
     *
     * @return void
     */
    public function testSaveSchema()
    {
        $schemaFile = tempnam(TMP, '__test');
        $this->DbAdminShell->params['output'] = $schemaFile;
        $this->DbAdminShell->saveSchema();
        $this->assertFileExists($schemaFile);
        unlink($schemaFile);
        $this->assertFileNotExists($schemaFile);
    }

    /**
     * Test checkSchema method
     *
     * @return void
     */
    public function testCheckSchema()
    {
        $res = $this->DbAdminShell->checkSchema();
        $this->assertFalse($res);
    }


    public function initInputProvider()
    {
        return [
            ['n', true],
            ['y', false],
        ];
    }

    /**
     * Test init method
     *
     * @return void
     * @dataProvider initInputProvider
     * @covers \BEdita\Core\Shell\DbAdminShell::init
     * @covers \BEdita\Core\Shell\DbAdminShell::checkSchema
     * @covers \BEdita\Core\Shell\Task\DbInitTaskShell::main
     * @covers \BEdita\Core\Utility\Database::currentSchema
     */
    public function testInit($userInput, $emptySchema)
    {
        $this->io->method('askChoice')
             ->willReturn($userInput);

        $info = Database::basicInfo();
        if ($info['vendor'] != 'mysql') {
            // TODO: DbAdminShell::init works only in MySQL
            return;
        }

        $this->DbAdminShell->init();

        if (!$emptySchema) {
            $res = $this->DbAdminShell->checkSchema();
            $this->assertTrue($res);
        }

        $schema = Database::currentSchema();
        if (!empty($schema)) {
            $res = Database::executeTransaction($this->dropTablesSql($schema));
            $this->assertNotEmpty($res);
            $this->assertEquals($res['success'], true);
        }

        if ($emptySchema) {
            $this->assertEmpty($schema);
        } else {
            $this->assertNotEmpty($schema);
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
