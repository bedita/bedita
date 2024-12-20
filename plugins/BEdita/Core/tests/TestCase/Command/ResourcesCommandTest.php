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
use BEdita\Core\Model\Entity\EndpointPermission;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

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
     * Test execute on `resources --help`
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::execute()
     * @covers ::getDescription()
     */
    public function testHelp()
    {
        $this->exec('resources add --help');
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
    public function testAddResource(): void
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
     * Data provider for `testAddDefault` test case.
     *
     * @return array
     */
    public function addProvider(): array
    {
        return [
            'role' => [
                true,
                'roles',
                'newrole',
            ],
            'app' => [
                true,
                'applications',
                'newapp',
                'description of the new app',
            ],
            'abort' => [
                'Resource name cannot be empty',
                'endpoints',
                '',
                null,
            ],
        ];
    }

    /**
     * Test `add` method
     *
     * @param bool|string $expected Expected success or error message.
     * @param string $type Resource type.
     * @param string $name Resource name.
     * @param string $description Resource description.
     * @return void
     * @dataProvider addProvider()
     * @covers ::getTable()
     * @covers ::setupDefaultEntity()
     */
    public function testAddDefault($expected, $type, $name, $description = ''): void
    {
        $input = array_filter(
            [$name, $description],
            function ($val) {
                return !is_null($val);
            }
        );
        $this->exec(sprintf('resources add -t %s', $type), $input);

        $exists = $this->fetchTable(Inflector::camelize($type))->exists(compact('name'));
        if ($expected === true) {
            static::assertTrue($exists);
            $this->assertExitCode(Command::CODE_SUCCESS);
            $this->assertErrorEmpty();
        } else {
            static::assertFalse($exists);
            $this->assertExitCode(Command::CODE_ERROR);
            $this->assertErrorContains($expected);
        }
    }

    /**
     * Data provider for `testAddPermission` test case.
     *
     * @return array
     */
    public function addPermissionProvider(): array
    {
        return [
            [
                '1',
                'home',
                '2',
                'mine',
                'block',
            ],
            [
                '1',
                '3',
                'first role',
                'true',
                'true',
            ],
        ];
    }

    /**
     * Test `add` method
     *
     * @param mixed $application Application name or id
     * @param mixed $endpoint Endpoint name or id
     * @param mixed $role Role name or id
     * @param string $read Read permission
     * @param string $write Write permission
     * @return void
     * @dataProvider addPermissionProvider
     * @covers ::getTable()
     * @covers ::setupEndpointPermissionEntity()
     */
    public function testAddPermission($application, $endpoint, $role, $read, $write): void
    {
        $this->exec('resources add -t endpoint_permissions', [$application, $endpoint, $role, $read, $write]);

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $endpointPermission = $this->fetchTable('EndpointPermissions')->find()->all()->last();

        $read = EndpointPermission::decode(EndpointPermission::encode($read));
        $write = EndpointPermission::decode(EndpointPermission::encode($write));

        static::assertSame($read, $endpointPermission->read);
        static::assertSame($write, $endpointPermission->write);
    }

    /**
     * Test edit resource
     *
     * @return void
     * @covers ::execute()
     */
    public function testEditResource(): void
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
     * Data provider for `testEdit` method.
     *
     * @return array
     */
    public function editProvider(): array
    {
        return [
            'Applications.api_key' => [
                'applications',
                'Disabled app',
                'api_key',
            ],
            'Applications.enabled' => [
                'applications',
                2,
                'enabled',
                '1',
            ],
        ];
    }

    /**
     * Test enable method
     *
     * @param string $type Resource type.
     * @param string|int $resId Resource ID or name.
     * @param string $field Field to be updated.
     * @param mixed|null $value New field value.
     * @return void
     * @dataProvider editProvider
     * @covers ::getEntity()
     * @covers ::getTable()
     */
    public function testEdit($type, $resId, $field, $value = null): void
    {
        $table = $this->fetchTable(Inflector::camelize($type));
        if (is_numeric($resId)) {
            $entity = $table->get($resId);
        } else {
            $entity = $table->find()->where(['name' => $resId])->firstOrFail();
        }
        $oldValue = $entity->get($field);

        $input = array_filter(
            [$value],
            function ($val) {
                return !is_null($val);
            }
        );
        $this->exec(sprintf('resources edit -t %s -f %s "%s"', $type, $field, $resId), $input);

        $newValue = $table->get($entity->id)->get($field);
        if ($value !== null) {
            $this->assertExitCode(Command::CODE_SUCCESS);
            $this->assertErrorEmpty();
            static::assertEquals($value, $newValue);
        } else {
            static::assertNotEquals($oldValue, $newValue);
        }
    }

    /**
     * Test `edit` failure
     *
     * @return void
     * @covers ::edit()
     */
    public function testEditFail(): void
    {
        $this->exec('resources edit -t applications -f description 1111');
        $this->assertErrorContains('Resource with id 1111 not found');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test list resources
     *
     * @return void
     * @covers ::execute()
     */
    public function testListResources(): void
    {
        $count = $this->fetchTable('Applications')->find()->count();
        $this->exec('resources ls --type=applications');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('%d result(s) found', $count));
        $this->assertOutputContains('First app');
        $this->assertOutputContains('Disabled app');
    }

    /**
     * Data provider for `testList` test case.
     *
     * @return array
     */
    public function listProvider(): array
    {
        return [
            'applications' => [
                2,
                'applications',
            ],
            'endpoints' => [
                3,
                'endpoints',
            ],
            'roles' => [
                2,
                'roles',
            ],
        ];
    }

    /**
     * Test ls method
     *
     * @param int $expected Expected count.
     * @param string $type Resource type.
     * @return void
     * @dataProvider listProvider()
     */
    public function testList($expected, $type): void
    {
        $this->exec(sprintf('resources ls -t %s', $type));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->assertOutputContains(sprintf('%d result(s) found', $expected));
    }

    /**
     * Test remove resource
     *
     * @return void
     * @covers ::execute()
     */
    public function testRmResource(): void
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

    /**
     * Data provider for `testRemove` test case.
     *
     * @return array
     */
    public function removeProvider(): array
    {
        return [
            'no confirm' => [
                false,
                2,
                'n',
            ],
            'confirm' => [
                true,
                2,
                'y',
            ],
            'not found' => [
                false,
                'this-app-does-not-exist',
                'y',
            ],
        ];
    }

    /**
     * Test rm method
     *
     * @param bool $expected Expected result.
     * @param int|string $id Resource ID or name.
     * @param string $answer Given answer (y/n).
     * @return void
     * @dataProvider removeProvider()
     * @covers ::getEntity()
     */
    public function testRemove($expected, $id, $answer): void
    {
        $countBefore = $this->fetchTable('Applications')->find()->count();

        $this->exec(sprintf('resources rm -t applications %s', $id), [$answer]);

        $countAfter = $this->fetchTable('Applications')->find()->count();

        if ($expected) {
            $this->assertExitCode(Command::CODE_SUCCESS);
            $this->assertErrorEmpty();
            static::assertSame($countBefore - 1, $countAfter);
        } else {
            $this->assertExitCode(Command::CODE_ERROR);
            static::assertSame($countBefore, $countAfter);
        }
    }

    /**
     * Test error on missing subcommand
     *
     * @return void
     * @covers ::execute()
     */
    public function testNoSubcommand(): void
    {
        $this->exec('resources');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Resources management command. Available subcommands: add, edit, ls, rm');
    }
}
