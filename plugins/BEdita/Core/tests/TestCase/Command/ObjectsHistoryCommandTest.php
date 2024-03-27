<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\ObjectsHistoryCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ObjectsHistoryCommand
 */
class ObjectsHistoryCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.History',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('objects_history --help');
        $this->assertOutputContains('--action');
        $this->assertOutputContains('Action to execute <comment>(default: read)</comment>');
        $this->assertOutputContains('<comment>(choices: read|delete)</comment>');
        $this->assertOutputContains('--id');
        $this->assertOutputContains('Filter history by resource id(s)');
        $this->assertOutputContains('--since');
        $this->assertOutputContains('Consider history since this date');
        $this->assertOutputContains('--type');
        $this->assertOutputContains('Filter history by type');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::read()
     * @covers ::fetchQuery()
     * @covers ::objectsIterator()
     */
    public function testExecute(): void
    {
        $this->exec('objects_history');
        $this->assertOutputContains('<info>Perform "read" on objects history</info>');
        $this->assertOutputContains('<success>Found 2 items</success>');
        $this->assertOutputContains('<info>Done</info>');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method, by types
     *
     * @return void
     * @covers ::execute()
     * @covers ::read()
     * @covers ::fetchQuery()
     * @covers ::objectsIterator()
     */
    public function testExecuteByTypes(): void
    {
        $this->exec('objects_history --type users --type documents');
        $this->assertOutputContains('<info>Perform "read" on objects history, for type(s) users,documents</info>');
        $this->assertOutputContains('<success>Found 2 items</success>');
        $this->assertOutputContains('<info>Done</info>');
        $this->assertExitSuccess();
    }

    /**
     * Test `delete` method, with exception
     *
     * @return void
     * @covers ::execute()
     * @covers ::delete()
     * @covers ::fetchQuery()
     * @covers ::objectsIterator()
     */
    public function testDeleteWithException(): void
    {
        $throwError = function () {
            throw new \Exception('An error');
        };
        // add listener to global event manager
        EventManager::instance()->on('Model.beforeDelete', $throwError);
        $this->exec('objects_history --action delete --id 2 --verbose');
        // ensure to off listener from global event manager
        EventManager::instance()->off('Model.beforeDelete', $throwError);
        $this->assertOutputContains('<info>Perform "delete" on objects history, for resource(s) id(s) 2</info>');
        $this->assertOutputContains('======> Deleting history item 1');
        $this->assertOutputContains('======> Deleting history item 2');
        $this->assertOutputContains('<success>Deleted 0 items [2 errors]</success>');
        $this->assertOutputContains('<info>Done</info>');
        $this->assertExitSuccess();
    }

    /**
     * Test `delete` method, with no exception
     *
     * @return void
     * @covers ::execute()
     * @covers ::delete()
     * @covers ::fetchQuery()
     * @covers ::objectsIterator()
     */
    public function testDeleteWithNoException(): void
    {
        $this->exec('objects_history --action delete --id 2 --verbose');
        $this->assertOutputContains('<info>Perform "delete" on objects history, for resource(s) id(s) 2</info>');
        $this->assertOutputContains('======> Deleting history item 1');
        $this->assertOutputContains('======> Deleting history item 2');
        $this->assertOutputContains('<success>Deleted 2 items [0 errors]</success>');
        $this->assertOutputContains('<info>Done</info>');
        $this->assertExitSuccess();
    }
}
