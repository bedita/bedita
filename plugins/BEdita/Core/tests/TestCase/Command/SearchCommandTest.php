<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Command\SearchCommand Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\SearchCommand
 */
class SearchCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test `buildOptionParser` method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('search --help');
        $this->assertOutputContains('Interface to handle search indexes and data');
        $this->assertOutputContains('Clear index');
        $this->assertOutputContains('Delete an object from index');
        $this->assertOutputContains('Index a single object');
        $this->assertOutputContains('Reindex all objects in the system');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::operation()
     */
    public function testExecute(): void
    {
        $this->exec('search');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `operation` method
     *
     * @return void
     * @covers ::operation()
     */
    public function testOperation(): void
    {
        $this->exec('search --reindex');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `reindex` method
     *
     * @return void
     * @covers ::reindex()
     */
    public function testReindex(): void
    {
        $this->exec('search --reindex');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->exec('search --index');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `delete` method
     *
     * @return void
     * @covers ::delete()
     */
    public function testDelete(): void
    {
        $this->exec('search --delete');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `clear` method
     *
     * @return void
     * @covers ::clear()
     */
    public function testClear(): void
    {
        $this->exec('search --clear');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }
}
