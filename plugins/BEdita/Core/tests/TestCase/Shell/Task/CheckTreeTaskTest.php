<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Shell\Task\CheckTreeTask;
use Cake\Console\Shell;
use Cake\Database\Driver\Mysql;
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
    public function setUp(): void
    {
        parent::setUp();

        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
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
            ['checkRules' => false, '_primary' => false]
        );

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
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

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
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

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
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

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
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

        $this->exec(sprintf('%s --verbose', CheckTreeTask::class));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('There are no folders that are not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('Found 1 objects that are present multiple times within same parent!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is present multiple times within parent <info>root-folder</info> (#<info>11</info>)');
    }
}
