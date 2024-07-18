<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\TreeRecoverCommand;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Database\Expression\ComparisonExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Command\TreeRecoverCommand} Test Case
 *
 * @property \BEdita\Core\Model\Table\TreesTable $Trees
 * @property \BEdita\Core\Model\Table\CategoriesTable $Categories
 * @covers \BEdita\Core\Command\TreeRecoverCommand
 */
#[\AllowDynamicProperties]
class TreeRecoverCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use LocatorAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->cleanupConsoleTrait();
        $this->useCommandRunner();
        $this->Trees = $this->fetchTable('Trees');
        $this->Categories = $this->fetchTable('Categories');
    }

    /**
     * Helper function to get a "snapshot" describing tree state.
     *
     * @param \Cake\ORM\Table $table
     * @return array
     */
    protected function getTreeState(Table $table): array
    {
        return $table->find()
            ->all()
            ->combine('id', function (EntityInterface $node) {
                return sprintf('%d / %d', $node['tree_left'], $node['tree_right']);
            })
            ->toArray();
    }

    /**
     * Corrupt tree: `UPDATE * FROM trees SET tree_left = id * 2, tree_right = id * 2 + 1`.
     *
     * @param \Cake\ORM\Table $table
     * @return void
     */
    protected function corruptTree(Table $table): void
    {
        $table->updateAll(
            [
                'tree_left' => new ComparisonExpression('id', 2, 'integer', '*'),
                'tree_right' => new ComparisonExpression(new ComparisonExpression('id', 2, 'integer', '*'), 1, 'integer', '+'),
            ],
            []
        );
    }

    /**
     * Test main execution.
     *
     * @return void
     */
    public function testExecute(): void
    {
        // Get current snapshot. This assumes fixtures cause tree to be in a valid state.
        $expected = $this->getTreeState($this->Trees);
        $this->corruptTree($this->Trees);

        // Check that some corruption actually happened.
        $corrupt = $this->getTreeState($this->Trees);
        static::assertNotEquals($expected, $corrupt, 'Tree hasn\'t been corrupted prior to testing');

        // Recover.
        $this->exec(TreeRecoverCommand::defaultName());

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Tree recovery completed');

        // Assert that tree returned to a valid state.
        $actual = $this->getTreeState($this->Trees);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test execution with `--categories` flag.
     *
     * @return void
     */
    public function testExecuteCategories(): void
    {
        // Get current snapshot. This assumes fixtures cause tree to be in a valid state.
        $expected = $this->getTreeState($this->Categories);
        $this->corruptTree($this->Categories);

        // Check that some corruption actually happened.
        $corrupt = $this->getTreeState($this->Categories);
        static::assertNotEquals($expected, $corrupt, 'Categories tree hasn\'t been corrupted prior to testing');

        // Recover.
        $this->exec(sprintf('%s --categories', TreeRecoverCommand::defaultName()));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Categories tree recovery completed');

        // Assert that tree returned to a valid state.
        $actual = $this->getTreeState($this->Categories);
        static::assertEquals($expected, $actual);
    }
}
