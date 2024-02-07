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
     */
    public function testExecute(): void
    {
        $this->exec('objects_delete --type documents');
        $this->assertOutputContains('Deleting from trash objects, since -1 month, for type documents');
        $this->assertOutputContains('Deleted from trash 0 objects [0 errors]');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }
}
