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

use BEdita\Core\Job\ServiceRegistry;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
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
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        ServiceRegistry::reset();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::getDescription()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('check_api_key --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Check API key');
        $this->assertOutputContains('cake check_api_key [-h] [-q] [-v]');
    }

    /**
     * Test check_api_key
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecute(): void
    {
        $this->exec('check_api_key --verbose');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('=====> Loading default application...');
        $this->assertOutputContains('<info>DONE</info>');
        $application = $this->fetchTable('Applications')->get(ApplicationsTable::DEFAULT_APPLICATION);
        $this->assertOutputContains(sprintf('=====> Default API key is: <info>%s</info>', $application->api_key));
        $this->assertOutputContains('=====> <success>API key is ok. You can now make your requests even more handsome with it!</success>');
    }

    /**
     * Test check_api_key with missing default application
     *
     * @return void
     * @covers ::execute()
     */
    public function testAbort(): void
    {
        // mock table Application so that get() throws RecordNotFoundException
        $table = $this->getMockBuilder(ApplicationsTable::class)
            ->onlyMethods(['get'])
            ->getMock();
        $table->method('get')
            ->willThrowException(new RecordNotFoundException());
        $this->getTableLocator()->set('Applications', $table);
        $this->exec('check_api_key --verbose');
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Default application is missing, please check your installation');
    }

    /**
     * Undocumented function
     *
     * @return void
     * @covers ::execute()
     */
    public function testEmptyApiKey(): void
    {
        // mock table Application so that get() throws RecordNotFoundException
        $application = new Application();
        $application->api_key = '';
        $table = $this->getMockBuilder(ApplicationsTable::class)
            ->onlyMethods(['get'])
            ->getMock();
        $table->method('get')
            ->willReturn($application);
        $this->getTableLocator()->set('Applications', $table);
        $this->exec('check_api_key');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('=====> <warning>Default application has no API key</warning>');
    }
}
