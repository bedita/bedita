<?php
namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Shell\Task\CheckTreeTask;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * @covers \BEdita\Core\Shell\Task\CheckTreeTask
 */
class CheckTreeTaskTest extends ConsoleIntegrationTestCase
{

    /**
     * Trees table.
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    public $Trees;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Trees = TableRegistry::get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trees);

        parent::tearDown();
    }

    /**
     * Test execution when tree is already valid.
     *
     * @return void
     */
    public function testExecutionOk()
    {
        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('There are no folders that are not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
    }

    /**
     * Test execution when there are folders that are not in the tree.
     *
     * @return void
     */
    public function testExecutionFolderNotInTree()
    {
        $this->Trees->delete(
            $this->Trees->find()
                ->where(['object_id' => 12])
                ->firstOrFail(),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('Found 1 folders not in tree!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is not in the tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
    }

    /**
     * Test execution when there are ubiquitous folders.
     *
     * @return void
     */
    public function testExecutionUbiquitousFolder()
    {
        $this->Trees->save(
            $this->Trees->newEntity([
                'object_id' => 12,
                'parent_id' => null,
            ]),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
        $this->assertOutputContains('Found 1 ubiquitous folders!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is ubiquitous');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
    }

    /**
     * Test execution when there are other objects that have children.
     *
     * @return void
     */
    public function testExecutionOtherObjectWithChildren()
    {
        $this->Trees->save(
            $this->Trees->newEntity([
                'object_id' => 4,
                'parent_id' => 2,
            ]),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('Found 1 other objects with children!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) has children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
    }

    /**
     * Test execution when there are other objects that have children.
     *
     * @return void
     */
    public function testExecutionObjectTwiceInFolder()
    {
        // Worse-than-worst case scenario: drop unique index to simulate a case where the constraint was violated somehow.
        $this->Trees->getConnection()
            ->execute('DROP INDEX trees_objectparent_uq ON trees');
        $this->Trees->save(
            $this->Trees->newEntity([
                'object_id' => 2,
                'parent_id' => 11,
            ]),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('Found 1 objects that are present multiple times within same parent!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is present multiple times within parent <info>root-folder</info> (#<info>11</info>)');
    }
}
