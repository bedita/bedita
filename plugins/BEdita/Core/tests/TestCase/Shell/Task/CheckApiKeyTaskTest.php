<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Model\Table\ApplicationsTable;
use BEdita\Core\Shell\Task\CheckApiKeyTask;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\Core\Shell\Task\CheckApiKeyTask
 */
class CheckApiKeyTaskTest extends ConsoleIntegrationTestCase
{
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
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Applications = TableRegistry::getTableLocator()->get('Applications');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Applications);

        parent::tearDown();
    }

    /**
     * Test execution when default application is missing.
     *
     * @return void
     *
     * @covers ::main()
     */
    public function testExecuteMissingApplication()
    {
        $this->Applications->deleteAll(['id' => ApplicationsTable::DEFAULT_APPLICATION]);

        $this->exec(CheckApiKeyTask::class);

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('Default application is missing, please check your installation');
    }

    /**
     * Test execution when default application has empty API key.
     *
     * @return void
     *
     * @covers ::main()
     */
    public function testExecuteApplicationEmptyApiKey()
    {
        $this->Applications->updateAll(['api_key' => ''], ['id' => ApplicationsTable::DEFAULT_APPLICATION]);

        $this->exec(CheckApiKeyTask::class);

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('Default application has no API key');
    }

    /**
     * Test execution when everything looks OK.
     *
     * @return void
     *
     * @covers ::main()
     */
    public function testExecuteOk()
    {
        $apiKey = $this->Applications->get(1)->api_key;

        $this->exec(CheckApiKeyTask::class);

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Default API key is: <info>%s</info>', $apiKey));
    }
}
