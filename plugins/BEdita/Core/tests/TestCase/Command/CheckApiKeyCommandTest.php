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

use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\CheckApiKeyCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\CheckApiKeyCommand
 */
class CheckApiKeyCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Applications table.
     *
     * @var \BEdita\Core\Model\Table\ApplicationsTable
     */
    public $Applications;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        $this->Applications = $this->fetchTable('Applications');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Applications);
        parent::tearDown();
    }

    /**
     * Test help
     *
     * @return void
     * @covers ::getDescription()
     */
    public function testHelp(): void
    {
        $this->exec('check_api_key --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Check API key');
        $this->assertOutputContains('cake check_api_key [-h] [-q] [-v]');
    }

    /**
     * Test execution when default application is missing.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteMissingApplication()
    {
        $this->Applications->deleteAll(['id' => ApplicationsTable::DEFAULT_APPLICATION]);

        $this->exec('check_api_key');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Default application is missing, please check your installation');
    }

    /**
     * Test execution when default application has empty API key.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteApplicationEmptyApiKey()
    {
        $this->Applications->updateAll(['api_key' => ''], ['id' => ApplicationsTable::DEFAULT_APPLICATION]);

        $this->exec('check_api_key');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertOutputContains('Default application has no API key');
    }

    /**
     * Test execution when everything looks OK.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteOk()
    {
        $apiKey = $this->Applications->get(1)->api_key;

        $this->exec('check_api_key');

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Default API key is: <info>%s</info>', $apiKey));
    }
}
