<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\ResourcesShell;
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
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.roles',
    ];

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \BEdita\API\Shell\ResourcesShell
     */
    public $ResourcesShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->ResourcesShell = new ResourcesShell($this->io);
        $this->ResourcesShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ResourcesShell);

        parent::tearDown();
    }

    /**
     * Data provider for `testAd` test case.
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
     * @param string $name Resource name
     * @param string $description Resource description
     * @param bool $success Operation success
     * @return void
     *
     * @dataProvider addProvider
     * @covers ::add()
     * @covers ::initModel()
     */
    public function testAdd($type, $name, $description = null, $success = true)
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $map = [
            ['Resource name', null, $name],
            ['Resource description (optional)', null, $description],
        ];
        $io->method('ask')
             ->will($this->returnValueMap($map));

        $res = $this->invoke(['resources', 'add', '-t', $type], [], $io);

        if ($success) {
            $id = $this->resourceIdByName(Inflector::camelize($type), $name);
            $this->assertTrue(!empty($id));
        } else {
            $this->assertAborted();
        }
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
                'Disabled app',
                'enabled',
                1,
            ],
        ];
    }

    /**
     * Test enable method
     *
     * @param int $id resource identifier
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
             ->will($this->returnValueMap($map));

        $res = $this->invoke(['resources', 'edit', '-t', $type, '-f', $field, $resId], [], $io);

        $entity = TableRegistry::get($modelName)->get($id);
        static::assertNotEquals($currValue, $entity->get($field));
        if ($value) {
            static::assertEquals($value, $entity->get($field));
        }
        // restore value
        $entity->$field = $currValue;
        TableRegistry::get($modelName)->save($entity);
    }

    /**
     * Test ls method
     *
     * @return void
     *
     * @covers ::ls()
     */
    public function testLs()
    {
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $res = $this->invoke(['resources', 'ls', '-t', 'applications'], [], $io);
        static::assertEquals(count($res), 2);

        $res = $this->invoke(['resources', 'ls', '-t', 'endpoints'], [], $io);
        static::assertEquals(count($res), 3);

        $res = $this->invoke(['resources', 'ls', '-t', 'roles'], [], $io);
        static::assertEquals(count($res), 2);
    }

    /*
     * Test rm method
     *
     * @dataProvider idsProvider
     * @covers ::rm()
     * @covers ::getEntity()
     */
    public function testRm()
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
             ->will($this->returnValueMap($mapChoice));

        $res = $this->invoke(['resources', 'rm', '-t', 'applications', $entity->id], [], $io);
        static::assertFalse($res);

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $mapChoice = [
            [$inMsg, ['y', 'n'], 'n', 'y']
        ];
        $io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));

        $res = $this->invoke(['resources', 'rm', '-t', 'applications', $entity->id], [], $io);
        static::assertTrue($res);

        $notFound = false;
        try {
            TableRegistry::get('Applications')->get($entity->id);
        } catch (RecordNotFoundException $e) {
            $notFound = true;
        }
        $this->assertTrue($notFound);
    }

    /**
     * Return resource id find by $name name
     *
     * @param string $name resource name
     * @return int $id resource identifier
     */
    private function resourceIdByName($modelName, $name)
    {
        return TableRegistry::get($modelName)->find()->where(['name' => $name])->firstOrFail()->id;
    }
}
