<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * \BEdita\Core\Shell\ResourcesShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\ResourcesShell
 */
class ResourcesShellTest extends ShellTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.endpoint_permissions',
    ];

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Data provider for `testAddDefault` test case.
     *
     * @return array
     */
    public function addProvider()
    {
        return [
            'role' => [
                'roles',
                'newrole'
            ],
            'app' => [
                'applications',
                'newapp',
                'description of the new app'
            ],
            'abort' => [
                'endpoints',
                '',
                '',
                false
            ],
        ];
    }

    /**
     * Test `add` method
     *
     * @param string $type Resource type
     * @param string $name Resource name
     * @param string $description Resource description
     * @param bool $success Operation success
     * @return void
     *
     * @dataProvider addProvider
     * @covers ::add()
     * @covers ::initModel()
     * @covers ::setupDefaultEntity()
     */
    public function testAddDefault($type, $name, $description = null, $success = true)
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $map = [
            ['Resource name', null, $name],
            ['Resource description (optional)', null, $description],
        ];
        $io->method('ask')
             ->will(static::returnValueMap($map));

        $res = $this->invoke(['resources', 'add', '-t', $type], [], $io);

        if ($success) {
            $table = Inflector::camelize($type);
            $id = $this->resourceIdByName($table, $name);
            static::assertTrue(!empty($id));
            TableRegistry::get($table)->delete($res);
        } else {
            $this->assertAborted();
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
            'perms1' => [
                1,
                'home',
                2,
                'mine',
                'block'
            ],
            'perms2' => [
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
        $type = 'endpoint_permissions';
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $map = [
            ['Applications id or name', null, $application],
            ['Endpoints id or name', null, $endpoint],
            ['Roles id or name', null, $role],
        ];
        $io->method('ask')
             ->will(static::returnValueMap($map));

        $perms = ['true', 'false', 'block', 'mine'];
        $mapChoice = [
            ["'read' permission", $perms, null, $read],
            ["'write' permission", $perms, null, $write],
        ];
        $io->method('askChoice')
             ->will(static::returnValueMap($mapChoice));

        $res = $this->invoke(['resources', 'add', '-t', $type], [], $io);

        static::assertNotEmpty($res);
        $testRead = $res->read;
        if (is_bool($testRead)) {
            $testRead = $testRead ? 'true' : 'false';
        }
        static::assertEquals($testRead, $read);

        $testWrite = $res->write;
        if (is_bool($testWrite)) {
            $testWrite = $testWrite ? 'true' : 'false';
        }
        static::assertEquals($testWrite, $write);

        TableRegistry::get('EndpointPermissions')->delete($res);
    }

    /**
     * Data provider for `testEdit` method.
     *
     * @return array
     */
    public function editProvider()
    {
        return [
            'appApiKey' => [
                'applications',
                'Disabled app',
                'api_key',
            ],
            'appEnable' => [
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
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $modelName = Inflector::camelize($type);
        $id = $resId;
        if (!is_numeric($id)) {
            $id = $this->resourceIdByName($modelName, $id);
        }
        $entity = TableRegistry::get($modelName)->get($id);
        $currValue = $entity->get($field);

        $inMsg = sprintf('New value for "%s" [current is "%s"]', $field, $currValue);
        $map = [
            [$inMsg, null, $value]
        ];
        $io->method('ask')
             ->will(static::returnValueMap($map));

        $this->invoke(['resources', 'edit', '-t', $type, '-f', $field, $resId], [], $io);

        $entity = TableRegistry::get($modelName)->get($id);
        static::assertNotEquals($currValue, $entity->get($field));
        if ($value) {
            static::assertEquals($value, $entity->get($field));
        }
        // restore value
        $entity->set($field, $currValue);
        TableRegistry::get($modelName)->save($entity);
    }

    /**
     * Test ls method
     *
     * @return void
     *
     * @covers ::ls()
     */
    public function testList()
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $res = $this->invoke(['resources', 'ls', '-t', 'applications'], [], $io);
        static::assertEquals(count($res), 2);

        $res = $this->invoke(['resources', 'ls', '-t', 'endpoints'], [], $io);
        static::assertEquals(count($res), 3);

        $res = $this->invoke(['resources', 'ls', '-t', 'roles'], [], $io);
        static::assertEquals(count($res), 2);
    }

    /**
     * Test rm method
     *
     * @covers ::rm()
     * @covers ::getEntity()
     */
    public function testRemove()
    {
        $entity = TableRegistry::get('Applications')->newEntity();
        $entity->name = 'a-new-app';
        $entity->description = 'make apps great again';
        TableRegistry::get('Applications')->save($entity);

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $inMsg = sprintf('You are REMOVING "applications" with name or id "%s" - are you sure?', $entity->id);
        $mapChoice = [
            [$inMsg, ['y', 'n'], 'n']
        ];
        $io->method('askChoice')
             ->will(static::returnValueMap($mapChoice));

        $res = $this->invoke(['resources', 'rm', '-t', 'applications', $entity->id], [], $io);
        static::assertFalse($res);

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $mapChoice = [
            [$inMsg, ['y', 'n'], 'n', 'y']
        ];
        $io->method('askChoice')
             ->will(static::returnValueMap($mapChoice));

        $res = $this->invoke(['resources', 'rm', '-t', 'applications', $entity->id], [], $io);
        static::assertTrue($res);

        $notFound = false;
        try {
            TableRegistry::get('Applications')->get($entity->id);
        } catch (RecordNotFoundException $e) {
            $notFound = true;
        }
        static::assertTrue($notFound);
    }

    /**
     * Return resource id find by $name name
     *
     * @param string $modelName Model name.
     * @param string $name Resource name.
     * @return int $id Resource identifier.
     */
    private function resourceIdByName($modelName, $name)
    {
        return TableRegistry::get($modelName)->find()->where(['name' => $name])->firstOrFail()->id;
    }
}
