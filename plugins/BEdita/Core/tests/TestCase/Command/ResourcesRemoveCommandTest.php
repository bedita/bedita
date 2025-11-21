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

use BEdita\Core\Command\ResourcesRemoveCommand;
use BEdita\Core\Job\ServiceRegistry;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see BEdita\Core\Command\ResourcesRemoveCommand} Test Case
 */
#[CoversClass(ResourcesRemoveCommand::class)]
class ResourcesRemoveCommandTest extends TestCase
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
     */
    public function testBuildOptionParser()
    {
        $this->exec('resources_remove --help');
        $this->assertOutputContains('Resources remove');
        $this->assertOutputContains('cake resources_remove [-h] [-q] -t applications|roles|endpoints|endpoint_permissions [-v] <name|id>');
        $this->assertOutputContains('Entity type');
        $this->assertOutputContains('applications|roles|endpoints|endpoint_permissions');
        $this->assertOutputContains('Arguments');
        $this->assertOutputContains('Resource\'s name or id');
    }

    /**
     * Data provider for `testExecute` test case.
     *
     * @return array
     */
    public static function executeProvider(): array
    {
        return [
            'remove application (n)' => [
                'Disabled app',
                'applications',
                ['n'],
                0,
            ],
            'remove application (y)' => [
                'Disabled app',
                'applications',
                ['y'],
                1,
            ],
            'remove role (n)' => [
                'second role',
                'roles',
                ['n'],
                0,
            ],
            'remove role (y)' => [
                'second role',
                'roles',
                ['y'],
                1,
            ],
            'remove endpoint (n)' => [
                'disabled',
                'endpoints',
                ['n'],
                0,
            ],
            'remove endpoint (y)' => [
                'disabled',
                'endpoints',
                ['y'],
                1,
            ],
            'remove endpoint permission (n)' => [
                '1',
                'endpoint_permissions',
                ['n'],
                0,
            ],
            'remove endpoint permission (y)' => [
                '1',
                'endpoint_permissions',
                ['y'],
                1,
            ],
        ];
    }

    /**
     * Test modify resource
     *
     * @param string $resourceId Resource ID.
     * @param string $resourceType Resource type.
     * @param array $input Input data.
     * @param int $expectedCountDiff Expected resource count difference.
     * @return void
     */
    #[DataProvider('executeProvider')]
    public function testExecute(string $resourceId, string $resourceType, array $input, int $expectedCountDiff): void
    {
        $tableName = Inflector::camelize($resourceType);
        $table = $this->fetchTable($tableName);
        $count = $table->find()->count();
        $expected = $count - $expectedCountDiff;
        $field = is_numeric($resourceId) ? 'id' : 'name';
        $this->exec(sprintf('resources_remove "%s" --type %s', $resourceId, $resourceType), $input);
        $actual = $table->find()->count();
        $this->assertEquals($expected, $actual);
        if ($expectedCountDiff === 1) {
            $this->assertOutputContains(sprintf('Record "%s" deleted', $resourceId));
            $this->assertExitCode(Command::CODE_SUCCESS);
        } else {
            $this->assertExitCode(Command::CODE_ERROR);
            $this->assertOutputContains('No action performed');
            $resource = $table->find()->where([$field => $resourceId])->firstOrFail();
            $this->assertNotEmpty($resource);
        }
    }

    /**
     * Test remove resource with wrong type
     *
     * @return void
     */
    public function testExecuteWrongType(): void
    {
        $this->exec('resources_remove "First app" --type wrong', ['y']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('`wrong` is not a valid value for `--type`. Please use one of `applications|roles|endpoints|endpoint_permissions`');
    }

    /**
     * Test remove resource not found
     *
     * @return void
     */
    public function testExecuteResourceNotFound(): void
    {
        $this->exec('resources_remove 999 --type applications', ['y']);
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Resource with id 999 not found');
    }
}
