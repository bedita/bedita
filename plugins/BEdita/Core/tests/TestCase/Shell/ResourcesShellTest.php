<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Model\Entity\EndpointPermission;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;
use Cake\Utility\Inflector;

/**
 * \BEdita\Core\Shell\ResourcesShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\ResourcesShell
 */
class ResourcesShellTest extends ConsoleIntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.endpoint_permissions',
    ];

    /**
     * Data provider for `testAddDefault` test case.
     *
     * @return array
     */
    public function addProvider()
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
     *
     * @dataProvider addProvider()
     * @covers ::add()
     * @covers ::initModel()
     * @covers ::setupDefaultEntity()
     */
    public function testAddDefault($expected, $type, $name, $description = '')
    {
        $input = array_filter(
            [$name, $description],
            function ($val) {
                return !is_null($val);
            }
        );
        $this->exec(sprintf('resources add -t %s', $type), $input);

        $exists = TableRegistry::get(Inflector::camelize($type))->exists(compact('name'));
        if ($expected === true) {
            static::assertTrue($exists);
            $this->assertExitCode(Shell::CODE_SUCCESS);
            $this->assertErrorEmpty();
        } else {
            static::assertFalse($exists);
            $this->assertExitCode(Shell::CODE_ERROR);
            $this->assertErrorContains($expected);
        }
    }

    /**
     * Data provider for `testAddPermission` test case.
     *
     * @return array
     */
    public function addPermissionProvider()
    {
        return [
            [
                1,
                'home',
                2,
                'mine',
                'block'
            ],
            [
                1,
                3,
                'first role',
                'true',
                'true'
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
     *
     * @dataProvider addPermissionProvider
     * @covers ::add()
     * @covers ::initModel()
     * @covers ::setupEndpointPermissionEntity()
     */
    public function testAddPermission($application, $endpoint, $role, $read, $write)
    {
        $this->exec('resources add -t endpoint_permissions', [$application, $endpoint, $role, $read, $write]);

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $endpointPermission = TableRegistry::get('EndpointPermissions')->find()->last();

        $read = EndpointPermission::decode(EndpointPermission::encode($read));
        $write = EndpointPermission::decode(EndpointPermission::encode($write));

        static::assertSame($read, $endpointPermission->read);
        static::assertSame($write, $endpointPermission->write);
    }

    /**
     * Data provider for `testEdit` method.
     *
     * @return array
     */
    public function editProvider()
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
                1,
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
     *
     * @dataProvider editProvider
     * @covers ::edit()
     * @covers ::getEntity()
     * @covers ::initModel()
     */
    public function testEdit($type, $resId, $field, $value = null)
    {
        $table = TableRegistry::get(Inflector::camelize($type));
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
            $this->assertExitCode(Shell::CODE_SUCCESS);
            $this->assertErrorEmpty();
            static::assertEquals($value, $newValue);
        } else {
            static::assertNotEquals($oldValue, $newValue);
        }
    }

    /**
     * Data provider for `testList` test case.
     *
     * @return array
     */
    public function listProvider()
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
     *
     * @dataProvider listProvider()
     * @covers ::ls()
     */
    public function testList($expected, $type)
    {
        $this->exec(sprintf('resources ls -t %s', $type));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->assertOutputContains(sprintf('%d result(s) found', $expected));
    }

    /**
     * Data provider for `testRemove` test case.
     *
     * @return array
     */
    public function removeProvider()
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
     *
     * @dataProvider removeProvider()
     * @covers ::rm()
     * @covers ::getEntity()
     */
    public function testRemove($expected, $id, $answer)
    {
        $countBefore = TableRegistry::get('Applications')->find()->count();

        $this->exec(sprintf('resources rm -t applications %s', $id), [$answer]);

        $countAfter = TableRegistry::get('Applications')->find()->count();

        if ($expected) {
            $this->assertExitCode(Shell::CODE_SUCCESS);
            $this->assertErrorEmpty();
            static::assertSame($countBefore - 1, $countAfter);
        } else {
            $this->assertExitCode(Shell::CODE_ERROR);
            static::assertSame($countBefore, $countAfter);
        }
    }
}
