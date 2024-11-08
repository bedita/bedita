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
 * {@see BEdita\Core\Command\ResourcesModifyCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ResourcesModifyCommand
 */
class ResourcesModifyCommandTest extends TestCase
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
        'plugin.BEdita/Core.Roles',
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
        $this->exec('resources_modify --help');
        $this->assertOutputContains('Resources modify');
        $this->assertOutputContains('cake resources_modify -f api_key|description|enabled|name|unchangeable [-h] [-q] -t applications|roles|endpoints [-v] <name|id>');
        $this->assertOutputContains('Field name');
        $this->assertOutputContains('api_key|description|enabled|name|unchangeable');
        $this->assertOutputContains('Entity type');
        $this->assertOutputContains('applications|roles|endpoints');
        $this->assertOutputContains('Arguments');
        $this->assertOutputContains('Resource\'s name or id');
    }

    /**
     * Data provider for `testExecute` test case.
     *
     * @return array
     */
    public function executeProvider(): array
    {
        return [
            'modify application api_key' => [
                'First app',
                'applications',
                'api_key',
                [],
                [
                    'name' => 'First app',
                ],
            ],
            'modify application description' => [
                'First app',
                'applications',
                'description',
                ['A sample description'],
                [
                    'name' => 'First app',
                    'description' => 'A sample description',
                ],
            ],
            'modify role' => [
                'first role',
                'roles',
                'description',
                [
                    'Another description for first role',
                ],
                [
                    'name' => 'first role',
                    'description' => 'Another description for first role',
                ],
            ],
            'modify endpoint' => [
                'home',
                'endpoints',
                'description',
                [
                    'Another description for home endpoint',
                ],
                [
                    'name' => 'home',
                    'description' => 'Another description for home endpoint',
                ],
            ],
        ];
    }

    /**
     * Test modify resource
     *
     * @param string $resourceId Resource ID.
     * @param string $resourceType Resource type.
     * @param string $resourceField Resource field.
     * @param array $input Input data.
     * @param array $expectedResource Expected resource data.
     * @return void
     * @covers ::execute()
     * @dataProvider executeProvider()
     */
    public function testExecute(string $resourceId, string $resourceType, string $resourceField, array $input, array $expectedResource): void
    {
        $tableName = Inflector::camelize($resourceType);
        $table = $this->fetchTable($tableName);
        $originalApiKey = null;
        $field = is_numeric($resourceId) ? 'id' : 'name';
        if ($resourceField === 'api_key') {
            $originalApiKey = $table->find()->where([$field => $resourceId])->firstOrFail()->get('api_key');
        }
        $this->exec(sprintf('resources_modify "%s" --type %s --field %s', $resourceId, $resourceType, $resourceField), $input);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $resource = $table->find()->where([$field => $resourceId])->firstOrFail();
        $this->assertOutputContains(sprintf('Resource with id %d modified', $resource->id));
        $this->assertNotEmpty($resource);
        foreach ($expectedResource as $field => $value) {
            $this->assertEquals($value, $resource->get($field));
        }
        if ($resourceField === 'api_key') {
            $newApiKey = $resource->get('api_key');
            $this->assertNotEquals($originalApiKey, $newApiKey);
        }
    }

    /**
     * Test modify resource with missing type required options
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteEmptyType(): void
    {
        $this->exec('resources_modify "First app" --field description', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Missing required option. The `type` option is required and has no default value');
    }

    /**
     * Test modify resource with missing field required options
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteEmptyField(): void
    {
        $this->exec('resources_modify "First app" --type applications', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Missing required option. The `field` option is required and has no default value');
    }

    /**
     * Test modify resource with wrong type
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteWrongType(): void
    {
        $this->exec('resources_modify "First app" --type wrong --field description', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('"wrong" is not a valid value for --type. Please use one of "applications, roles, endpoints"');
    }

    /**
     * Test modify resource with wrong field
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteWrongField(): void
    {
        $this->exec('resources_modify "First app" --type applications --field wrong', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('"wrong" is not a valid value for --field. Please use one of "api_key, description, enabled, name, unchangeable"');
    }

    /**
     * Test modify resource with invalid field
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteResourceNotFound(): void
    {
        $this->exec('resources_modify 999 --type applications --field description', ['A sample description']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Resource with id 999 not found');
    }
}
