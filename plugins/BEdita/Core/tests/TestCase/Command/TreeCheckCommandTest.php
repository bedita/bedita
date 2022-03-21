<?php
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\TreeCheckCommand;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ModelAwareTrait;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Command\TreeCheckCommand} Test Case.
 *
 * @property \BEdita\Core\Model\Table\TreesTable $Trees
 *
 * @covers \BEdita\Core\Command\TreeCheckCommand
 */
class TreeCheckCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use ModelAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->useCommandRunner();
        $this->loadModel('Trees');
    }

    /**
     * Test execution when tree is already valid.
     *
     * @return void
     */
    public function testExecutionOk()
    {
        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('There are no folders not in tree');
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
            ['checkRules' => false, '_primary' => false]
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('Found 1 folders not in tree!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is not in the tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
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

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('Found 1 ubiquitous folders!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is ubiquitous');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
    }

    /**
     * Test execution when there are other objects that are roots.
     *
     * @return void
     */
    public function testExecutionOtherObjectInRoot()
    {
        $this->Trees->save(
            $this->Trees->newEntity([
                'object_id' => 2,
                'parent_id' => null,
            ]),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('Found 1 other objects in root!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is a root');
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

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
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
        if (!($this->Trees->getConnection()->getDriver() instanceof Mysql)) {
            static::markTestSkipped('This test requires dropping a constraint and happens only on MySQL');
        }

        $this->Trees->getConnection()->execute(
            sprintf('DROP INDEX %s ON %s', 'trees_objectparent_uq', $this->Trees->getTable())
        );
        $this->Trees->save(
            $this->Trees->newEntity([
                'object_id' => 2,
                'parent_id' => 11,
            ]),
            ['checkRules' => false]
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('Found 1 objects that are present multiple times within same parent!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is positioned multiple times within the same parent');
    }
}
