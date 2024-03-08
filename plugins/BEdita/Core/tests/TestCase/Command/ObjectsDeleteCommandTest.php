<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
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
 * {@see BEdita\Core\Command\ObjectsDeleteCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ObjectsDeleteCommand
 */
class ObjectsDeleteCommandTest extends TestCase
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
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
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
    public function testBuildOptionParser()
    {
        $this->exec('objects_delete --help');
        $this->assertOutputContains('Delete objects in trash since this date');
        $this->assertOutputContains('Delete objects in trash by type');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::deleteObject()
     */
    public function testExecute(): void
    {
        $this->exec('objects_delete --type documents');
        $this->assertOutputContains('Deleting from trash objects, since -1 month, for type(s) documents');
        $this->assertOutputContains('Deleted from trash 2 objects [0 errors]');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::deleteObject()
     */
    public function testExecuteSince(): void
    {
        $this->exec('objects_delete --since "-1 weeks"');
        $this->assertOutputContains('Deleting from trash objects, since -1 weeks');
        $this->assertOutputContains('Deleted from trash 2 objects [0 errors]');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::objectsIterator()
     * @covers ::deleteObject()
     */
    public function testExecuteError(): void
    {
        $throwError = function () {
            throw new \Exception('An error');
        };

        // add listener to global event manager
        EventManager::instance()->on('Model.beforeDelete', $throwError);

        // count trash items
        $expected = $this->fetchTable('objects')->find()->where(['deleted' => 1])->count();

        // run command
        $this->exec('objects_delete --since "-1 weeks"');

        // ensure to off listener from global event manager
        EventManager::instance()->off('Model.beforeDelete', $throwError);

        // count new trash items
        $actual = $this->fetchTable('objects')->find()->where(['deleted' => 1])->count();

        // test assertion
        self::assertSame($expected, $actual);
        $this->assertOutputContains('Deleting from trash objects, since -1 weeks');
        $this->assertOutputContains('Deleted from trash 0 objects [2 errors]');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }
}
