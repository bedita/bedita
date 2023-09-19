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
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
    ];

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
     * Test `reindex` method
     *
     * @return void
     * @covers ::reindex()
     * @covers ::doMultiIndex()
     * @covers ::saveIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testReindex(): void
    {
        $this->exec('search --reindex');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `reindex` method
     *
     * @return void
     * @covers ::reindex()
     * @covers ::doMultiIndex()
     * @covers ::saveIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testReindexByTypes(): void
    {
        $this->exec('search --reindex documents,profiles');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `clear` method
     *
     * @return void
     * @covers ::clear()
     * @covers ::doMultiIndex()
     * @covers ::removeIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testClear(): void
    {
        $this->exec('search --clear');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `clear` method by types
     *
     * @return void
     * @covers ::clear()
     * @covers ::doMultiIndex()
     * @covers ::removeIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testClearByType(): void
    {
        $this->exec('search --clear documents,profiles');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::saveIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testIndex(): void
    {
        $this->exec('search --index 2');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `index` method on missing ID
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::saveIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testIndexMissingId(): void
    {
        $this->exec('search --index');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `index` method on wrong ID
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::saveIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testIndexWrongId(): void
    {
        $this->exec('search --index abcdefghi');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `delete` method
     *
     * @return void
     * @covers ::delete()
     * @covers ::doSingleIndex()
     * @covers ::removeIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testDelete(): void
    {
        $this->exec('search --delete 2');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `delete` method on missing ID
     *
     * @return void
     * @covers ::delete()
     * @covers ::doSingleIndex()
     * @covers ::removeIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testDeleteMissingId(): void
    {
        $this->exec('search --delete');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `delete` method on wrong ID
     *
     * @return void
     * @covers ::delete()
     * @covers ::doSingleIndex()
     * @covers ::removeIndexEntity()
     * @covers ::doIndexResource()
     */
    public function testDeleteWrongId(): void
    {
        $this->exec('search --delete abcdefghi');
        $this->assertExitCode(Command::CODE_ERROR);
    }
}
