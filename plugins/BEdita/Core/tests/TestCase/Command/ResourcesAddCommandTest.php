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
 * {@see BEdita\Core\Command\ResourcesAddCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ResourcesAddCommand
 */
class ResourcesAddCommandTest extends TestCase
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
    public function testBuildOptionParser()
    {
        $this->exec('resources_add --help');
        $this->assertOutputContains('Resources add');
        $this->assertOutputContains('cake resources_add [-h] [-q] -t applications|roles|endpoints|endpoint_permissions [-v]');
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
            'add application' => [
                'applications',
                [
                    'test-app',
                    'test app description',
                ],
                [
                    'name' => 'test-app',
                    'description' => 'test app description',
                ],
            ],
            'add role' => [
                'roles',
                [
                    'test-role',
                    'test role description',
                ],
                [
                    'name' => 'test-role',
                    'description' => 'test role description',
                ],
            ],
            'add endpoint' => [
                'endpoints',
                [
                    'test-endpoint',
                    'test endpoint description',
                ],
                [
                    'name' => 'test-endpoint',
                    'description' => 'test endpoint description',
                ],
            ],
            'endpoint_permissions' => [
                'endpoint_permissions',
                [
                    'First app', // application
                    'home', // endpoint
                    '1', // role
                    'mine',
                    'mine',
                ],
                [
                    'application_id' => 1,
                    'endpoint_id' => 2,
                    'role_id' => 1,
                    'permission' => 5,
                ],
            ],
        ];
    }

    /**
     * Test add resource
     *
     * @param string $resourceType Resource type.
     * @param array $input Input data.
     * @param array $expectedResource Expected resource data.
     * @return void
     * @covers ::execute()
     * @covers ::setupDefaultEntity()
     * @covers ::setupEndpointPermissionEntity()
     * @dataProvider executeProvider()
     */
    public function testExecute(string $resourceType, array $input, array $expectedResource): void
    {
        $tableName = Inflector::camelize($resourceType);
        $table = $this->fetchTable($tableName);
        $expected = $table->find()->count() + 1;
        $this->exec(sprintf('resources_add --type=%s', $resourceType), $input);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $actual = $table->find()->count();
        $this->assertEquals($expected, $actual);
        $resource = $table->find()->all()->last();
        $this->assertNotEmpty($resource);
        foreach ($expectedResource as $field => $value) {
            $this->assertEquals($value, $resource->get($field));
        }
    }

    /**
     * Test add resource with missing type required options
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteEmptyType(): void
    {
        $this->exec('resources_add "First app"', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Missing required option. The `type` option is required and has no default value');
    }

    /**
     * Test add resource with wrong type
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteWrongType(): void
    {
        $this->exec('resources_add "First app" --type wrong', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('"wrong" is not a valid value for --type. Please use one of "applications, roles, endpoints, endpoint_permissions"');
    }

    /**
     * Test add resource with missing name
     *
     * @return void
     * @covers ::execute()
     * @covers ::setupDefaultEntity()
     */
    public function testResourceNameEmpty(): void
    {
        $this->exec('resources_add --type applications', ['']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Resource name cannot be empty');
    }
}
