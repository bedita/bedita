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
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\ResourcesCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ResourcesCommand
 */
class ResourcesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
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
     */
    public function testBuildOptionParser()
    {
        $this->exec('resources --help');
        $this->assertOutputContains('Resources management command. Available subcommands: add, edit, ls, rm');
        $this->assertOutputContains('cake resources [-f api_key|description|enabled|name|unchangeable] [-q] [-h] [-q] [-t] [-v] [<add|edit|ls|rm>] [<name|id>]');
        $this->assertOutputContains('Field name');
        $this->assertOutputContains('api_key|description|enabled|name|unchangeable)');
        $this->assertOutputContains('List entities filtered by comma separated key=value pairs');
        $this->assertOutputContains('Entity type');
        $this->assertOutputContains('Subcommand to perform');
        $this->assertOutputContains('(choices: add|edit|ls|rm)');
        $this->assertOutputContains('Resource\'s name or id');
    }

    /**
     * Test add resource
     *
     * @return void
     * @covers ::execute()
     */
    public function testAdd(): void
    {
        $expected = $this->fetchTable('Applications')->find()->count() + 1;
        $this->exec('resources add --type=applications', ['dummy-app', 'Dummy Application']);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $actual = $this->fetchTable('Applications')->find()->count();
        $this->assertEquals($expected, $actual);
        $application = $this->fetchTable('Applications')->find()->all()->last();
        $this->assertNotEmpty($application);
        $this->assertEquals('dummy-app', $application->get('name'));
        $this->assertEquals('Dummy Application', $application->get('description'));
    }

    /**
     * Test list resources
     *
     * @return void
     * @covers ::execute()
     */
    public function testList(): void
    {
        $count = $this->fetchTable('Applications')->find()->count();
        $this->exec('resources ls --type=applications');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('%d result(s) found', $count));
        $this->assertOutputContains('First app');
        $this->assertOutputContains('Disabled app');
    }

    /**
     * Test edit resource
     *
     * @return void
     * @covers ::execute()
     */
    public function testEdit(): void
    {
        $table = $this->fetchTable('Applications');
        $table->save($table->newEntity([
            'name' => 'dummy-app',
            'description' => 'Dummy Application',
        ]));
        $application = $table->find()->where(['name' => 'dummy-app'])->firstOrFail();
        $id = $application->get('id');
        $this->exec(sprintf('resources edit %d --field description --type=applications', $id), ['My Dummy Application']);
        $application = $table->find()->where(['name' => 'dummy-app'])->firstOrFail();
        $this->assertNotEmpty($application);
        $this->assertEquals('dummy-app', $application->get('name'));
        $this->assertEquals('My Dummy Application', $application->get('description'));
    }

    /**
     * Test remove resource
     *
     * @return void
     * @covers ::execute()
     */
    public function testRm(): void
    {
        $table = $this->fetchTable('Applications');
        $table->save($table->newEntity([
            'name' => 'dummy-app',
            'description' => 'Dummy Application',
        ]));
        $actual = $table->find()->count();
        $expected = $actual - 1;
        $application = $table->find()->where(['name' => 'dummy-app'])->firstOrFail();
        $id = $application->get('id');
        $this->exec(sprintf('resources rm %d --type=applications', $id), ['y']);
        $actual = $table->find()->count();
        $this->assertEquals($expected, $actual);
    }
}
