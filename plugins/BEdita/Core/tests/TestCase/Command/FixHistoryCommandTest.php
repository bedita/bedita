<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
 * {@see BEdita\Core\Command\FixHistoryCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\FixHistoryCommand
 */
class FixHistoryCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
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
        'plugin.BEdita/Core.History',
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
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('fix_history --help');
        $this->assertOutputContains('Min ID to check');
        $this->assertOutputContains('Max ID to check');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::fixHistoryCreate()
     * @covers ::fixHistoryUpdate()
     * @covers ::historyEntity()
     * @covers ::missingHistoryQuery()
     * @covers ::objectsGenerator()
     */
    public function testExecute(): void
    {
        $this->exec('fix_history');
        $this->assertExitSuccess();
        $this->assertOutputContains('History creation items fixed: 14');
        $this->assertOutputContains('History update items fixed: 1');
    }

    /**
     * Test `execute` with `id` and `type` option
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectDetails()
     * @covers ::joinConditions()
     * @covers ::missingHistoryQuery()
     */
    public function testOptionsExecute(): void
    {
        $this->exec('fix_history --from 1 --to 5');
        $this->assertExitSuccess();
        $this->assertOutputContains('History creation items fixed: 4');
        $this->assertOutputContains('History update items fixed: 1');
    }
}
