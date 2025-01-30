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
use Cake\Utility\Inflector;

/**
 * {@see BEdita\Core\Command\ResourcesListCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ResourcesListCommand
 */
class ResourcesListCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
    ];

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
    public function testBuildOptionParser()
    {
        $this->exec('resources_list --help');
        $this->assertOutputContains('Resources list');
        $this->assertOutputContains('cake resources_list [--filter] [-h] [-q] -t applications|roles|endpoints|endpoint_permissions [-v]');
        $this->assertOutputContains('List entities filtered by comma separated key=value pairs');
        $this->assertOutputContains('Entity type');
        $this->assertOutputContains('applications|roles|endpoints|endpoint_permissions');
    }

    /**
     * Data provider for `testExecute` test case.
     *
     * @return array
     */
    public function executeProvider(): array
    {
        return [
            'applications, no filter' => [
                'applications',
                '',
                '{n} result(s) found',
            ],
            'applications, filter' => [
                'applications',
                'name="First app"',
                '1 result(s) found',
            ],
            'endpoints, no filter' => [
                'endpoints',
                '',
                '{n} result(s) found',
            ],
            'endpoints, filter' => [
                'endpoints',
                'name="home"',
                '1 result(s) found',
            ],
            'roles, no filter' => [
                'roles',
                '',
                '{n} result(s) found',
            ],
            'roles, filter' => [
                'roles',
                'name="first role"',
                '1 result(s) found',
            ],
            'endpoint_permissions, no filter' => [
                'endpoint_permissions',
                '',
                '{n} result(s) found',
            ],
            'endpoint_permissions, filter' => [
                'endpoint_permissions',
                'endpoint_id=1',
                '1 result(s) found',
            ],
        ];
    }

    /**
     * Test modify resource
     *
     * @param string $resourceType Resource type.
     * @param string $resourceFilter Resource ID.
     * @param string $expected Expected output.
     * @return void
     * @covers ::execute()
     * @dataProvider executeProvider()
     */
    public function testExecute(string $resourceType, string $resourceFilter, string $expected): void
    {
        $tableName = Inflector::camelize($resourceType);
        $command = sprintf('resources_list --type %s', $resourceType);
        if (!empty($resourceFilter)) {
            $command .= sprintf(' --filter %s', $resourceFilter);
        } else {
            $table = $this->fetchTable($tableName);
            $count = $table->find()->count();
            $expected = str_replace('{n}', (string)$count, $expected);
        }
        $this->exec($command);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains($expected);
    }

    /**
     * Test list resource with wrong type
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteWrongType(): void
    {
        $this->exec('resources_list --type wrong', ['y']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('`wrong` is not a valid value for `--type`. Please use one of `applications, roles, endpoints, endpoint_permissions`');
    }
}
